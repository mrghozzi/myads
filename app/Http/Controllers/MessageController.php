<?php

namespace App\Http\Controllers;

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

            $query = [];
            $requestedId = $request->query('id', $request->query('user'));
            if ($requestedId) {
                $query['id'] = $requestedId;
            }

            return redirect()->route('messages.index', $query);
        }

        $partner = null;
        $requestedId = $request->query('id', $request->query('user'));
        if ($requestedId) {
            $partner = $this->conversations->resolvePartner($requestedId, $user);
        }

        return view('theme::messages.index', $this->buildPageData($user, $partner));
    }

    public function create(Request $request, UserPrivacyService $privacy)
    {
        $recipient = $request->query('recipient');
        if ($recipient) {
            $recipientUser = User::where('username', $recipient)->first();
            if ($recipientUser && !$privacy->canDirectMessage($recipientUser, Auth::user())) {
                return redirect()->route('messages.index')->withErrors(['recipient' => __('messages.direct_messages_disabled')]);
            }
        }

        return view('theme::messages.create', compact('recipient'));
    }

    public function store(
        Request $request,
        UserPrivacyService $privacy,
        SecurityPolicyService $securityPolicy,
        SecurityThrottleService $securityThrottle,
        NotificationService $notifications
    ) {
        $request->validate([
            'recipient' => 'required|exists:users,username',
            'message' => 'required|string|max:2000',
        ]);

        $user = $request->user();
        $recipient = User::where('username', $request->recipient)->firstOrFail();
        if (!$privacy->canDirectMessage($recipient, $user)) {
            return back()->withErrors(['recipient' => __('messages.direct_messages_disabled')])->withInput();
        }

        $text = trim((string) $request->input('message'));
        if ($violation = $securityPolicy->textViolation($text, 'messages')) {
            return back()->withErrors(['message' => $violation])->withInput();
        }

        if ($cooldownMessage = $securityThrottle->actionMessage($user, 'private_message')) {
            return back()->withErrors(['message' => $cooldownMessage])->withInput();
        }

        $message = $this->createMessage($user, $recipient, $text);
        $securityThrottle->hitAction($user, 'private_message');
        $this->sendMessageNotification($notifications, $recipient, $user, $message);

        return redirect()->route('messages.show', $this->conversations->conversationRouteKey($user, $recipient));
    }

    public function show($id, UserPrivacyService $privacy)
    {
        $user = Auth::user();
        $partner = $this->conversations->resolvePartner($id, $user);
        abort_unless($privacy->canDirectMessage($partner, $user), 403);

        return view('theme::messages.index', $this->buildPageData($user, $partner));
    }

    public function history(Request $request, $id)
    {
        $user = $request->user();
        $partner = $this->conversations->resolvePartner($id, $user);
        $beforeId = (int) $request->query('before_id', 0);
        $limit = max(1, min(50, (int) $request->query('limit', MessageConversationService::PAGE_SIZE)));

        if ($beforeId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid before_id',
            ], 422);
        }

        $messages = $this->conversations->olderMessages($user, $partner, $beforeId, $limit);
        $oldestId = $messages->first()->id_msg ?? $beforeId;
        $hasMore = $messages->isNotEmpty()
            ? $this->conversations->hasOlderMessages($user, $partner, $oldestId)
            : false;
        $boundaryContext = $this->conversations->boundaryContext($user, $partner, $messages);

        return response()->json([
            'success' => true,
            'html' => $this->renderConversation($messages, $partner, $user, true, $boundaryContext),
            'oldest_id' => $oldestId,
            'has_more' => $hasMore,
            'count' => $messages->count(),
        ]);
    }

    public function load(Request $request, $id)
    {
        $user = $request->user();
        $partner = $this->conversations->resolvePartner($id, $user);

        if ($request->has('after_id')) {
            $afterId = (int) $request->query('after_id', 0);
            $messages = $this->conversations->newerMessages($user, $partner, $afterId);
            if ($messages->isNotEmpty()) {
                $this->conversations->markConversationAsRead($user, $partner);
            }

            $boundaryContext = $this->conversations->boundaryContext($user, $partner, $messages);

            return response()->json([
                'success' => true,
                'html' => $this->renderConversation($messages, $partner, $user, true, $boundaryContext),
                'latest_id' => $messages->isNotEmpty() ? $messages->last()->id_msg : $afterId,
                'count' => $messages->count(),
            ]);
        }

        $messages = $this->conversations->recentMessages($user, $partner);
        $this->conversations->markConversationAsRead($user, $partner);
        $boundaryContext = $this->conversations->boundaryContext($user, $partner, $messages);

        return view('theme::messages.partials.conversation', [
            'messages' => $messages,
            'partner' => $partner,
            'user' => $user,
            ...$boundaryContext,
        ]);
    }

    public function updates(Request $request)
    {
        $user = $request->user();
        $conversationKey = trim((string) $request->query('conversation', ''));
        $afterId = (int) $request->query('after_id', 0);
        $toastAfterId = (int) $request->query('toast_after_id', $afterId);

        $partner = $conversationKey !== ''
            ? $this->conversations->resolvePartner($conversationKey, $user)
            : null;

        $activeMessages = collect();
        $activeThreadHtml = '';
        $latestId = $afterId;

        if ($partner) {
            $activeMessages = $this->conversations->newerMessages($user, $partner, $afterId);
            if ($activeMessages->isNotEmpty()) {
                $this->conversations->markConversationAsRead($user, $partner);
                $latestId = (int) $activeMessages->last()->id_msg;
            }

            $boundaryContext = $this->conversations->boundaryContext($user, $partner, $activeMessages);
            $activeThreadHtml = $this->renderConversation($activeMessages, $partner, $user, true, $boundaryContext);
        }

        [$paged] = $this->conversations->paginatedConversations($user);

        return response()->json([
            'success' => true,
            'unread_count' => $this->conversations->unreadConversationCount($user),
            'latest_id' => $latestId,
            'latest_global_id' => $this->conversations->latestVisibleMessageId($user),
            'conversations_html' => view('theme::messages.partials.conversations', [
                'conversations' => $paged,
                'partner' => $partner,
            ])->render(),
            'active_thread' => [
                'html' => $activeThreadHtml,
                'count' => $activeMessages->count(),
                'latest_id' => $latestId,
            ],
            'toast' => $this->conversations->latestIncomingToast($user, $toastAfterId),
        ]);
    }

    public function conversations(Request $request)
    {
        $user = $request->user();
        $conversationKey = trim((string) $request->query('conversation', ''));

        $partner = $conversationKey !== ''
            ? $this->conversations->resolvePartner($conversationKey, $user)
            : null;

        [$paged] = $this->conversations->paginatedConversations($user);

        return response()->json([
            'success' => true,
            'html' => view('theme::messages.partials.conversations', [
                'conversations' => $paged,
                'partner' => $partner,
            ])->render(),
            'has_more' => $paged->hasMorePages(),
        ]);
    }

    public function send(
        Request $request,
        $id,
        UserPrivacyService $privacy,
        SecurityPolicyService $securityPolicy,
        SecurityThrottleService $securityThrottle,
        NotificationService $notifications
    ) {
        $validator = $this->buildMessageValidator($request);
        if ($validator->fails()) {
            return $this->validationErrorResponse($request, $validator);
        }

        $user = $request->user();
        $partner = $this->conversations->resolvePartner($id, $user);
        if (!$privacy->canDirectMessage($partner, $user)) {
            return $this->messageErrorResponse($request, __('messages.direct_messages_disabled'), 403);
        }

        $text = trim((string) $request->input('message', ''));
        if ($violation = $securityPolicy->textViolation($text, 'messages')) {
            return $this->messageErrorResponse($request, $violation, 422);
        }

        if ($cooldownMessage = $securityThrottle->actionMessage($user, 'private_message')) {
            return $this->messageErrorResponse($request, $cooldownMessage, 429);
        }

        try {
            [$attachmentPath, $attachmentName, $attachmentSize] = $this->storeMessageAttachment($request);
        } catch (\Throwable) {
            return $this->messageErrorResponse($request, __('messages.upload_failed'), 500, 'attachment');
        }

        $message = $this->createMessage($user, $partner, $text, $attachmentPath, $attachmentName, $attachmentSize);
        $securityThrottle->hitAction($user, 'private_message');
        $this->sendMessageNotification($notifications, $partner, $user, $message);

        if (!$this->shouldReturnJson($request)) {
            return redirect()->route('messages.show', $this->conversations->conversationRouteKey($user, $partner));
        }

        $messageCollection = collect([$message]);
        $boundaryContext = $this->conversations->boundaryContext($user, $partner, $messageCollection);

        return response()->json([
            'success' => true,
            'html' => $this->renderConversation($messageCollection, $partner, $user, true, $boundaryContext),
            'latest_id' => $message->id_msg,
            'latest_global_id' => $this->conversations->latestVisibleMessageId($user),
        ]);
    }

    public function attachment(Request $request, $id)
    {
        $user = $request->user();

        $message = Message::whereKey($id)
            ->where(function ($query) use ($user) {
                $query->where('us_env', $user->id)
                    ->orWhere('us_rec', $user->id);
            })
            ->firstOrFail();

        $relativePath = ltrim((string) $message->attachment_path, '/\\');
        if ($relativePath === '') {
            abort(404);
        }

        $normalizedPath = str_replace(['..', '\\'], ['', '/'], $relativePath);
        $storageFile = storage_path('app/' . $normalizedPath);
        $legacyPublicFile = public_path($normalizedPath);

        if (is_file($storageFile)) {
            $filePath = $storageFile;
        } elseif (is_file($legacyPublicFile)) {
            $filePath = $legacyPublicFile;
        } else {
            abort(404);
        }

        $downloadName = trim((string) $message->attachment_name);
        if ($downloadName === '') {
            $downloadName = basename($filePath);
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        if ($request->boolean('download')) {
            return response()->download($filePath, $downloadName);
        }

        if ($request->boolean('inline') || str_starts_with($mimeType, 'image/')) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        }

        return response()->download($filePath, $downloadName);
    }

    private function buildPageData(User $user, ?User $partner = null): array
    {
        [$paged, $summaries] = $this->conversations->paginatedConversations($user);
        $partner ??= $summaries[0]['user'] ?? null;

        $messages = collect();
        if ($partner) {
            $messages = $this->conversations->recentMessages($user, $partner);
            $this->conversations->markConversationAsRead($user, $partner);
        }

        $boundaryContext = $partner
            ? $this->conversations->boundaryContext($user, $partner, $messages)
            : [
                'hasPreviousConversationMessage' => false,
                'precedingMessageEncrypted' => false,
            ];

        return [
            'conversations' => $paged,
            'partner' => $partner,
            'messages' => $messages,
            'hasOlderMessages' => $partner && $messages->isNotEmpty()
                ? $this->conversations->hasOlderMessages($user, $partner, $messages->first()->id_msg)
                : false,
            'partnerConversationRouteKey' => $partner ? $this->conversations->conversationRouteKey($user, $partner) : null,
            'latestGlobalMessageId' => $this->conversations->latestVisibleMessageId($user),
            'unreadConversationCount' => $this->conversations->unreadConversationCount($user),
            ...$boundaryContext,
        ];
    }

    private function renderConversation($messages, ?User $partner, User $user, bool $itemsOnly = false, array $context = []): string
    {
        return view('theme::messages.partials.conversation', array_merge([
            'messages' => $messages,
            'partner' => $partner,
            'user' => $user,
            'itemsOnly' => $itemsOnly,
        ], $context))->render();
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
        $message->state = 3;
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
            route('messages.show', $this->conversations->conversationRouteKey($recipient, $sender), false),
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

    private function shouldReturnJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax() || $request->wantsJson();
    }

    private function validationErrorResponse(Request $request, $validator)
    {
        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        return back()->withErrors($validator)->withInput();
    }

    private function messageErrorResponse(Request $request, string $message, int $status, string $field = 'message')
    {
        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        return back()->withErrors([$field => $message])->withInput();
    }
}
