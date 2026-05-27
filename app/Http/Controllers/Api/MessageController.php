<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageConversationService;
use App\Services\NotificationService;
use App\Services\SecurityPolicyService;
use App\Services\SecurityThrottleService;
use App\Services\UserPrivacyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ConversationResource;

class MessageController extends Controller
{
    private const MAX_ATTACHMENT_SIZE_KB = 5120;
    private const ATTACHMENT_MIME_TYPES = 'jpg,jpeg,png,gif,webp,pdf,zip,rar,7z,doc,docx,xls,xlsx,ppt,pptx,txt,csv';

    private ?bool $messageAttachmentColumnsReady = null;

    public function __construct(private readonly MessageConversationService $conversations)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($request->boolean('mark_all_read')) {
            $this->conversations->markAllAsRead($user);
            return response()->json(['success' => true]);
        }

        [$paged] = $this->conversations->paginatedConversations($user);

        // Map conversations into the format the mobile app expects
        $mapped = collect($paged->items())->map(function ($conv) use ($user) {
            $partner = $conv['user'];
            $message = $conv['message'];

            // Count unread messages from this partner
            $unreadCount = \App\Models\Message::where('us_env', $partner->id)
                ->where('us_rec', $user->id)
                ->where('state', '!=', 0)
                ->count();

            return [
                'user' => [
                    'id'       => $partner->id,
                    'username' => $partner->username,
                    'name'     => $partner->name ?? $partner->username,
                    'img'      => $partner->img ?? '',
                    'online'   => (bool) ($partner->online ?? false),
                    'verified' => (bool) ($partner->verified ?? false),
                ],
                'last_message' => [
                    'text' => $message->text ?? '',
                    'time' => $message->time ?? 0,
                ],
                'unread_count' => $unreadCount,
                'route_key'    => $conv['route_key'] ?? '',
            ];
        })->values();

        return response()->json([
            'success'       => true,
            'conversations' => $mapped,
            'unread_count'  => $this->conversations->unreadConversationCount($user),
            'has_more'      => $paged->hasMorePages(),
        ]);
    }

    public function show($identifier)
    {
        $user = Auth::user();
        $partner = $this->conversations->resolvePartner($identifier, $user);
        
        if (!$partner) {
            return response()->json(['error' => 'User not found'], 404);
        }

        abort_unless(app(UserPrivacyService::class)->canDirectMessage($partner, $user), 403);

        $messages = $this->conversations->recentMessages($user, $partner);
        $this->conversations->markConversationAsRead($user, $partner);

        return response()->json([
            'success' => true,
            'partner' => [
                'id'          => $partner->id,
                'username'    => $partner->username,
                'name'        => $partner->name,
                'avatar'      => $partner->img ?? '',
                'online'      => (bool) ($partner->online ?? false),
                'is_verified' => (bool) ($partner->verified ?? false),
            ],
            'messages' => $messages,
        ]);
    }

    public function store(
        Request $request,
        $identifier,
        UserPrivacyService $privacy,
        SecurityPolicyService $securityPolicy,
        SecurityThrottleService $securityThrottle,
        NotificationService $notifications
    ) {
        $user = $request->user();
        $partner = $this->conversations->resolvePartner($identifier, $user);
        
        if (!$partner) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$privacy->canDirectMessage($partner, $user)) {
            return response()->json(['error' => __('messages.direct_messages_disabled')], 403);
        }

        $validator = $this->buildMessageValidator($request);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $text = trim((string) $request->input('message', ''));
        if ($violation = $securityPolicy->textViolation($text, 'messages')) {
            return response()->json(['error' => $violation], 422);
        }

        if ($cooldownMessage = $securityThrottle->actionMessage($user, 'private_message')) {
            return response()->json(['error' => $cooldownMessage], 429);
        }

        try {
            [$attachmentPath, $attachmentName, $attachmentSize] = $this->storeMessageAttachment($request);
        } catch (\Throwable) {
            return response()->json(['error' => __('messages.upload_failed')], 500);
        }

        $message = $this->createMessage($user, $partner, $text, $attachmentPath, $attachmentName, $attachmentSize);
        $securityThrottle->hitAction($user, 'private_message');
        $this->sendMessageNotification($notifications, $partner, $user, $message);

        return response()->json([
            'success' => true,
            'data' => $message
        ], 201);
    }

    public function markAsRead($identifier)
    {
        $user = Auth::user();
        $partner = $this->conversations->resolvePartner($identifier, $user);
        
        if (!$partner) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $this->conversations->markConversationAsRead($user, $partner);

        return response()->json(['success' => true, 'message' => 'Messages marked as read']);
    }

    public function updates(Request $request)
    {
        $user = $request->user();
        $conversationKey = trim((string) $request->query('conversation', ''));
        $afterId = (int) $request->query('after_id', 0);
        
        $partner = $conversationKey !== ''
            ? $this->conversations->resolvePartner($conversationKey, $user)
            : null;

        $activeMessages = collect();
        $latestId = $afterId;

        if ($partner) {
            $activeMessages = $this->conversations->newerMessages($user, $partner, $afterId);
            if ($activeMessages->isNotEmpty()) {
                $this->conversations->markConversationAsRead($user, $partner);
                $latestId = (int) $activeMessages->last()->id_msg;
            }
        }

        return response()->json([
            'success' => true,
            'unread_count' => $this->conversations->unreadConversationCount($user),
            'latest_id' => $latestId,
            'active_messages' => $activeMessages,
        ]);
    }

    private function createMessage(
        User $sender,
        User $recipient,
        string $text,
        ?string $attachmentPath = null,
        ?string $attachmentName = null,
        ?int $attachmentSize = null
    ): Message {
        $message = new Message();
        $message->name = $sender->username ?? $sender->name ?? '';
        $message->us_env = $sender->id;
        $message->us_rec = $recipient->id;
        $message->text = $text;
        if ($this->supportsMessageAttachments()) {
            $message->attachment_path = $attachmentPath;
            $message->attachment_name = $attachmentName;
            $message->attachment_size = $attachmentSize;
        }
        $message->time = time();
        $message->state = 3; // 3 means unread based on web logic
        $message->save();

        return $message;
    }

    private function sendMessageNotification(
        NotificationService $notifications,
        User $recipient,
        User $sender,
        Message $message
    ): void {
        $notifications->send(
            $recipient,
            __('messages.message_notification', ['user' => $sender->username]),
            "/messages/" . $sender->username,
            'envelope',
            $sender->id,
            'new_message',
            false
        );
    }

    private function buildMessageValidator(Request $request)
    {
        $rules = [
            'message' => 'nullable|string|max:2000',
        ];

        if ($this->supportsMessageAttachments()) {
            $rules['attachment'] = 'nullable|file|max:' . self::MAX_ATTACHMENT_SIZE_KB . '|mimes:' . self::ATTACHMENT_MIME_TYPES;
        }

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $text = trim((string) $request->input('message', ''));
            if ($text === '' && !$request->hasFile('attachment')) {
                $validator->errors()->add(
                    'message',
                    __('validation.required', ['attribute' => __('messages.message')])
                );
            }

            if ($request->hasFile('attachment') && !$this->supportsMessageAttachments()) {
                $validator->errors()->add(
                    'attachment',
                    'Attachment upload is not enabled yet. Please run the latest database migration.'
                );
            }
        });

        return $validator;
    }

    private function storeMessageAttachment(Request $request): array
    {
        if (!$request->hasFile('attachment')) {
            return [null, null, null];
        }

        $file = $request->file('attachment');
        $originalName = $file->getClientOriginalName();
        $fileSize = (int) $file->getSize();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');
        $filename = 'msg_' . time() . '_' . Str::random(10) . ($extension ? '.' . $extension : '');
        $relativeDirectory = 'message_attachments';
        $destinationPath = storage_path('app/' . $relativeDirectory);

        if (!is_dir($destinationPath) && !mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
            throw new \RuntimeException('Unable to create message attachment directory.');
        }

        $file->move($destinationPath, $filename);

        return [
            $relativeDirectory . '/' . $filename,
            $originalName,
            $fileSize,
        ];
    }

    private function supportsMessageAttachments(): bool
    {
        if ($this->messageAttachmentColumnsReady !== null) {
            return $this->messageAttachmentColumnsReady;
        }

        $this->messageAttachmentColumnsReady = Schema::hasColumn('messages', 'attachment_path')
            && Schema::hasColumn('messages', 'attachment_name')
            && Schema::hasColumn('messages', 'attachment_size');

        return $this->messageAttachmentColumnsReady;
    }
}
