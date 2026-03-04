<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    private const CONVERSATION_PAGE_SIZE = 25;
    private const MAX_ATTACHMENT_SIZE_KB = 5120;
    private const ATTACHMENT_MIME_TYPES = 'jpg,jpeg,png,gif,webp,pdf,zip,rar,7z,doc,docx,xls,xlsx,ppt,pptx,txt,csv';

    private ?bool $messageAttachmentColumnsReady = null;

    private function buildConversations($user)
    {
        $allMessages = Message::where('us_rec', $user->id)
            ->orWhere('us_env', $user->id)
            ->orderBy('time', 'desc')
            ->get();

        $partnerIds = [];
        foreach ($allMessages as $message) {
            $partnerId = $message->us_env == $user->id ? $message->us_rec : $message->us_env;
            if (!in_array($partnerId, $partnerIds, true)) {
                $partnerIds[] = $partnerId;
            }
        }

        $partners = User::whereIn('id', $partnerIds)->get()->keyBy('id');
        $unreadPartnerIds = Message::where('us_rec', $user->id)
            ->where('state', '!=', 0)
            ->groupBy('us_env')
            ->pluck('us_env')
            ->all();
        $unreadMap = array_flip($unreadPartnerIds);

        $conversations = [];
        $added = [];
        foreach ($allMessages as $message) {
            $partnerId = $message->us_env == $user->id ? $message->us_rec : $message->us_env;
            if (isset($added[$partnerId])) {
                continue;
            }
            $partner = $partners->get($partnerId);
            if (!$partner) {
                continue;
            }
            $added[$partnerId] = true;
            $conversations[] = [
                'user' => $partner,
                'message' => $message,
                'unread' => isset($unreadMap[$partnerId]),
            ];
        }

        $perPage = 9;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $collection = collect($conversations);
        $paged = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return [$paged, $conversations];
    }

    private function conversationQuery($user, $partner)
    {
        return Message::where(function ($query) use ($user, $partner) {
            $query->where('us_env', $user->id)
                ->where('us_rec', $partner->id);
        })->orWhere(function ($query) use ($user, $partner) {
            $query->where('us_env', $partner->id)
                ->where('us_rec', $user->id);
        });
    }

    private function getRecentConversationMessages($user, $partner, $limit = self::CONVERSATION_PAGE_SIZE)
    {
        return $this->conversationQuery($user, $partner)
            ->orderBy('id_msg', 'desc')
            ->limit($limit)
            ->get()
            ->sortBy('id_msg')
            ->values();
    }

    private function getOlderConversationMessages($user, $partner, $beforeId, $limit = self::CONVERSATION_PAGE_SIZE)
    {
        return $this->conversationQuery($user, $partner)
            ->where('id_msg', '<', $beforeId)
            ->orderBy('id_msg', 'desc')
            ->limit($limit)
            ->get()
            ->sortBy('id_msg')
            ->values();
    }

    private function getNewerConversationMessages($user, $partner, $afterId)
    {
        return $this->conversationQuery($user, $partner)
            ->where('id_msg', '>', $afterId)
            ->orderBy('id_msg', 'asc')
            ->get();
    }

    private function hasOlderConversationMessages($user, $partner, $oldestId)
    {
        if (!$oldestId) {
            return false;
        }

        return $this->conversationQuery($user, $partner)
            ->where('id_msg', '<', $oldestId)
            ->exists();
    }

    private function markConversationAsRead($user, $partner)
    {
        Message::where('us_env', $partner->id)
            ->where('us_rec', $user->id)
            ->where('state', '!=', 0)
            ->update(['state' => 0]);
    }

    private function resolvePartner($id, $user)
    {
        $partner = User::find($id);
        if ($partner) {
            return $partner;
        }

        $message = Message::whereKey($id)
            ->where(function ($query) use ($user) {
                $query->where('us_rec', $user->id)
                    ->orWhere('us_env', $user->id);
            })
            ->firstOrFail();

        $partnerId = $message->us_env == $user->id ? $message->us_rec : $message->us_env;
        return User::findOrFail($partnerId);
    }

    private function shouldReturnJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax() || $request->wantsJson();
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

    public function attachment(Request $request, $id)
    {
        $user = Auth::user();

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

        // Prevent traversal if the DB value is tampered with.
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
        $forceDownload = $request->boolean('download');
        $wantsInline = $request->boolean('inline');
        $isImage = str_starts_with($mimeType, 'image/');

        if ($forceDownload) {
            return response()->download($filePath, $downloadName);
        }

        if ($wantsInline || $isImage) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        }

        return response()->download($filePath, $downloadName);
    }

    public function index()
    {
        $user = Auth::user();

        if (request()->boolean('mark_all_read')) {
            Message::where('us_rec', $user->id)
                ->where('state', '!=', 0)
                ->update(['state' => 0]);

            $query = [];
            $requestedId = request()->query('id');
            if ($requestedId) {
                $query['id'] = $requestedId;
            }

            return redirect()->route('messages.index', $query);
        }

        [$paged, $conversations] = $this->buildConversations($user);

        $partner = null;
        $messages = collect();
        $requestedId = request()->query('id');
        if ($requestedId) {
            $partner = $this->resolvePartner($requestedId, $user);
        } elseif (!empty($conversations)) {
            $partner = $conversations[0]['user'];
        }

        if ($partner) {
            $messages = $this->getRecentConversationMessages($user, $partner);
            $this->markConversationAsRead($user, $partner);
        }

        return view('theme::messages.index', [
            'conversations' => $paged,
            'partner' => $partner,
            'messages' => $messages,
            'hasOlderMessages' => $partner && $messages->isNotEmpty()
                ? $this->hasOlderConversationMessages($user, $partner, $messages->first()->id_msg)
                : false,
        ]);
    }

    public function create(Request $request)
    {
        $recipient = $request->query('recipient');
        return view('theme::messages.create', compact('recipient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient' => 'required|exists:users,username',
            'message' => 'required|string|max:2000',
        ]);

        $recipient = User::where('username', $request->recipient)->firstOrFail();
        
        $message = new Message();
        $message->name = Auth::user()->username ?? Auth::user()->name ?? '';
        $message->us_env = Auth::id();
        $message->us_rec = $recipient->id;
        $message->text = $request->message;
        $message->time = time();
        $message->state = 3;
        $message->save();

        return redirect()->route('messages.show', $recipient->id)->with('success', __('message_sent'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $partner = $this->resolvePartner($id, $user);

        [$paged] = $this->buildConversations($user);
        $messages = $this->getRecentConversationMessages($user, $partner);
        $this->markConversationAsRead($user, $partner);

        return view('theme::messages.show', [
            'partner' => $partner,
            'messages' => $messages,
            'conversations' => $paged,
            'hasOlderMessages' => $messages->isNotEmpty()
                ? $this->hasOlderConversationMessages($user, $partner, $messages->first()->id_msg)
                : false,
        ]);
    }

    public function history(Request $request, $id)
    {
        $user = Auth::user();
        $partner = $this->resolvePartner($id, $user);

        $beforeId = (int) $request->query('before_id', 0);
        $limit = (int) $request->query('limit', self::CONVERSATION_PAGE_SIZE);
        $limit = max(1, min(50, $limit));

        if ($beforeId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid before_id',
            ], 422);
        }

        $messages = $this->getOlderConversationMessages($user, $partner, $beforeId, $limit);
        $oldestId = $messages->first()->id_msg ?? $beforeId;
        $hasMore = $messages->isNotEmpty()
            ? $this->hasOlderConversationMessages($user, $partner, $oldestId)
            : false;

        $html = view('theme::messages.partials.conversation', [
            'messages' => $messages,
            'partner' => $partner,
            'user' => $user,
            'itemsOnly' => true,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'oldest_id' => $oldestId,
            'has_more' => $hasMore,
            'count' => $messages->count(),
        ]);
    }

    public function load(Request $request, $id)
    {
        $user = Auth::user();
        $partner = $this->resolvePartner($id, $user);

        $afterId = (int) $request->query('after_id', 0);
        if ($request->has('after_id')) {
            $messages = $this->getNewerConversationMessages($user, $partner, $afterId);
            if ($messages->isNotEmpty()) {
                $this->markConversationAsRead($user, $partner);
            }

            $html = view('theme::messages.partials.conversation', [
                'messages' => $messages,
                'partner' => $partner,
                'user' => $user,
                'itemsOnly' => true,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'latest_id' => $messages->isNotEmpty() ? $messages->last()->id_msg : $afterId,
                'count' => $messages->count(),
            ]);
        }

        $messages = $this->getRecentConversationMessages($user, $partner);
        $this->markConversationAsRead($user, $partner);

        return view('theme::messages.partials.conversation', compact('messages', 'partner', 'user'));
    }

    public function send(Request $request, $id)
    {
        $validator = $this->buildMessageValidator($request);
        if ($validator->fails()) {
            if ($this->shouldReturnJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $partner = $this->resolvePartner($id, $user);
        $text = trim((string) $request->input('message', ''));

        try {
            [$attachmentPath, $attachmentName, $attachmentSize] = $this->storeMessageAttachment($request);
        } catch (\Throwable $e) {
            if ($this->shouldReturnJson($request)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.upload_failed'),
                ], 500);
            }

            return back()->withErrors(['attachment' => __('messages.upload_failed')])->withInput();
        }

        $message = new Message();
        $message->name = $user->username ?? $user->name ?? '';
        $message->us_env = $user->id;
        $message->us_rec = $partner->id;
        $message->text = $text;
        if ($this->supportsMessageAttachments()) {
            $message->attachment_path = $attachmentPath;
            $message->attachment_name = $attachmentName;
            $message->attachment_size = $attachmentSize;
        }
        $message->time = time();
        $message->state = 3;
        $message->save();

        if (!$this->shouldReturnJson($request)) {
            return redirect()->route('messages.show', $partner->id);
        }

        $html = view('theme::messages.partials.conversation', [
            'messages' => collect([$message]),
            'partner' => $partner,
            'user' => $user,
            'itemsOnly' => true,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'latest_id' => $message->id_msg,
        ]);
    }
}
