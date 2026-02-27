<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class MessageController extends Controller
{
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

    private function getConversationMessages($user, $partner)
    {
        $messages = Message::where(function ($query) use ($user, $partner) {
            $query->where('us_env', $user->id)
                ->where('us_rec', $partner->id);
        })->orWhere(function ($query) use ($user, $partner) {
            $query->where('us_env', $partner->id)
                ->where('us_rec', $user->id);
        })->orderBy('id_msg', 'desc')->get();

        Message::where('us_env', $partner->id)
            ->where('us_rec', $user->id)
            ->where('state', '!=', 0)
            ->update(['state' => 0]);

        return $messages;
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

    public function index()
    {
        $user = Auth::user();
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
            $messages = $this->getConversationMessages($user, $partner);
        }

        return view('theme::messages.index', [
            'conversations' => $paged,
            'partner' => $partner,
            'messages' => $messages,
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
        $messages = $this->getConversationMessages($user, $partner);

        return view('theme::messages.show', [
            'partner' => $partner,
            'messages' => $messages,
            'conversations' => $paged,
        ]);
    }

    public function load($id)
    {
        $user = Auth::user();
        $partner = $this->resolvePartner($id, $user);

        $messages = Message::where(function ($query) use ($user, $partner) {
            $query->where('us_env', $user->id)
                ->where('us_rec', $partner->id);
        })->orWhere(function ($query) use ($user, $partner) {
            $query->where('us_env', $partner->id)
                ->where('us_rec', $user->id);
        })->orderBy('id_msg', 'desc')->get();

        return view('theme::messages.partials.conversation', compact('messages', 'partner', 'user'));
    }

    public function send(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $partner = $this->resolvePartner($id, $user);

        $message = new Message();
        $message->name = $user->username ?? $user->name ?? '';
        $message->us_env = $user->id;
        $message->us_rec = $partner->id;
        $message->text = $request->message;
        $message->time = time();
        $message->state = 3;
        $message->save();

        $messages = Message::where(function ($query) use ($user, $partner) {
            $query->where('us_env', $user->id)
                ->where('us_rec', $partner->id);
        })->orWhere(function ($query) use ($user, $partner) {
            $query->where('us_env', $partner->id)
                ->where('us_rec', $user->id);
        })->orderBy('id_msg', 'desc')->get();

        return view('theme::messages.partials.conversation', compact('messages', 'partner', 'user'));
    }
}
