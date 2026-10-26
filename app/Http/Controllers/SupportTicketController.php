<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\SiteAdmin;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\AdminAccessService;
use App\Services\NotificationService;
use App\Services\SeoManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    protected function getSiteName(): string
    {
        return Option::where('name', 'titer')->value('o_valuer')
            ?? config('app.name', 'MyAds');
    }

    /**
     * Display the member's tickets list.
     */
    public function index(Request $request)
    {
        $siteName = $this->getSiteName();
        $user = Auth::user();

        $filter = $request->query('status', 'all');
        $query = SupportTicket::where('user_id', $user->id)
            ->with('messages')
            ->orderBy('updated_at', 'desc');

        if ($filter === 'open') {
            $query->whereIn('status', ['open', 'in_progress']);
        } elseif ($filter === 'answered') {
            $query->where('status', 'answered');
        } elseif ($filter === 'closed') {
            $query->whereIn('status', ['closed', 'resolved']);
        }

        $tickets = $query->paginate(10)->appends($request->query());

        $stats = [
            'total' => SupportTicket::where('user_id', $user->id)->count(),
            'open' => SupportTicket::where('user_id', $user->id)->whereIn('status', ['open', 'in_progress'])->count(),
            'answered' => SupportTicket::where('user_id', $user->id)->where('status', 'answered')->count(),
            'closed' => SupportTicket::where('user_id', $user->id)->whereIn('status', ['closed', 'resolved'])->count(),
        ];

        $pageTitle = __('support.support_tickets') . ' - ' . $siteName;
        $seo = app(SeoManager::class)->resolve($request);

        return view('theme::tickets.index', compact(
            'siteName',
            'tickets',
            'filter',
            'stats',
            'pageTitle',
            'seo'
        ));
    }

    /**
     * Show form to open a new support ticket.
     */
    public function create(Request $request)
    {
        $siteName = $this->getSiteName();
        $departments = [
            'technical' => __('support.dept_technical'),
            'billing' => __('support.dept_billing'),
            'account' => __('support.dept_account'),
            'general' => __('support.dept_general'),
        ];

        $priorities = [
            'low' => __('support.priority_low'),
            'medium' => __('support.priority_medium'),
            'high' => __('support.priority_high'),
            'urgent' => __('support.priority_urgent'),
        ];

        $pageTitle = __('support.open_new_ticket') . ' - ' . $siteName;
        $seo = app(SeoManager::class)->resolve($request);

        return view('theme::tickets.create', compact(
            'siteName',
            'departments',
            'priorities',
            'pageTitle',
            'seo'
        ));
    }

    /**
     * Store a new support ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|min:5|max:150',
            'department' => 'required|string|in:technical,billing,account,general',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'message' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support_attachments', 'public');
        }

        $ticketNumber = SupportTicket::generateTicketNumber();

        $ticket = SupportTicket::create([
            'ticket_number' => $ticketNumber,
            'user_id' => Auth::id(),
            'department' => $request->department,
            'priority' => $request->priority,
            'status' => 'open',
            'subject' => trim($request->subject),
            'last_reply_at' => now(),
            'last_reply_by' => Auth::id(),
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'is_admin' => false,
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        // Notify Admins
        try {
            $adminIds = SiteAdmin::where('is_active', true)->pluck('user_id')->toArray();
            if (!in_array(1, $adminIds)) {
                $adminIds[] = 1;
            }
            foreach ($adminIds as $adminId) {
                app(NotificationService::class)->send(
                    $adminId,
                    __('support.notif_ticket_created', [
                        'number' => $ticket->ticket_number,
                        'subject' => Str::limit($ticket->subject, 30),
                    ]),
                    route('admin.tickets.show', $ticket->id),
                    'headset'
                );
            }
        } catch (\Throwable $e) {}

        return redirect()->route('tickets.show', $ticket->ticket_number)
            ->with('success', __('support.ticket_created_success', ['number' => $ticket->ticket_number]));
    }

    /**
     * Show a support ticket discussion thread.
     */
    public function show(Request $request, string $ticketNumber)
    {
        $siteName = $this->getSiteName();
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        $canView = $ticket->user_id === Auth::id() ||
            Auth::user()->isSuperAdmin() ||
            app(AdminAccessService::class)->canAccess(Auth::user());

        if (!$canView) {
            abort(403);
        }

        $ticket->load(['user', 'messages.user']);

        $pageTitle = '#' . $ticket->ticket_number . ' - ' . $ticket->subject . ' - ' . $siteName;
        $seo = app(SeoManager::class)->resolve($request);

        return view('theme::tickets.show', compact(
            'siteName',
            'ticket',
            'pageTitle',
            'seo'
        ));
    }

    /**
     * Reply to an existing support ticket.
     */
    public function reply(Request $request, string $ticketNumber)
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        $canReply = $ticket->user_id === Auth::id() ||
            Auth::user()->isSuperAdmin() ||
            app(AdminAccessService::class)->canAccess(Auth::user());

        if (!$canReply) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|min:2',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support_attachments', 'public');
        }

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'is_admin' => false,
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        $ticket->update([
            'status' => 'open',
            'last_reply_at' => now(),
            'last_reply_by' => Auth::id(),
            'closed_at' => null,
            'closed_by' => null,
        ]);

        // Notify Admins
        try {
            $adminIds = SiteAdmin::where('is_active', true)->pluck('user_id')->toArray();
            if (!in_array(1, $adminIds)) {
                $adminIds[] = 1;
            }
            foreach ($adminIds as $adminId) {
                app(NotificationService::class)->send(
                    $adminId,
                    __('support.notif_ticket_replied_user', ['number' => $ticket->ticket_number]),
                    route('admin.tickets.show', $ticket->id),
                    'headset'
                );
            }
        } catch (\Throwable $e) {}

        return redirect()->route('tickets.show', $ticket->ticket_number)
            ->with('success', __('support.ticket_reply_success'));
    }

    /**
     * Close a support ticket.
     */
    public function close(Request $request, string $ticketNumber)
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();

        $canClose = $ticket->user_id === Auth::id() ||
            Auth::user()->isSuperAdmin() ||
            app(AdminAccessService::class)->canAccess(Auth::user());

        if (!$canClose) {
            abort(403);
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);

        return redirect()->route('tickets.show', $ticket->ticket_number)
            ->with('success', __('support.ticket_closed_success'));
    }
}
