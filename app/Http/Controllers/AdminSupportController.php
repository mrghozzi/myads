<?php

namespace App\Http\Controllers;

use App\Models\SupportCategory;
use App\Models\SupportTopic;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminSupportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /* -------------------------------------------------------------------------
     * SUPPORT TICKETS
     * ------------------------------------------------------------------------- */

    /**
     * List all incoming support tickets for administrators.
     */
    public function tickets(Request $request)
    {
        $query = SupportTicket::with(['user', 'messages'])->orderBy('updated_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(20)->appends($request->query());

        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'answered' => SupportTicket::where('status', 'answered')->count(),
            'closed' => SupportTicket::whereIn('status', ['closed', 'resolved'])->count(),
        ];

        return view('admin::admin.support.tickets.index', compact('tickets', 'stats'));
    }

    /**
     * Show ticket conversation details.
     */
    public function showTicket(Request $request, int $id)
    {
        $ticket = SupportTicket::with(['user', 'messages.user'])->findOrFail($id);

        return view('admin::admin.support.tickets.show', compact('ticket'));
    }

    /**
     * Admin reply to a support ticket.
     */
    public function replyTicket(Request $request, int $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'message' => 'required|string|min:2',
            'status' => 'nullable|string|in:open,in_progress,answered,resolved,closed',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support_attachments', 'public');
        }

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'is_admin' => true,
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        $newStatus = $request->status ?: 'answered';
        $ticket->update([
            'status' => $newStatus,
            'last_reply_at' => now(),
            'last_reply_by' => Auth::id(),
            'closed_at' => in_array($newStatus, ['closed', 'resolved']) ? now() : null,
            'closed_by' => in_array($newStatus, ['closed', 'resolved']) ? Auth::id() : null,
        ]);

        // Notify member via NotificationService
        try {
            app(NotificationService::class)->send(
                $ticket->user_id,
                __('support.notif_ticket_replied_admin', ['number' => $ticket->ticket_number]),
                route('tickets.show', $ticket->ticket_number),
                'headset'
            );
        } catch (\Throwable $e) {}

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', __('support.ticket_reply_success'));
    }

    /**
     * Update ticket status or priority.
     */
    public function updateTicketStatus(Request $request, int $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:open,in_progress,answered,resolved,closed',
            'priority' => 'required|string|in:low,medium,high,urgent',
        ]);

        $oldStatus = $ticket->status;
        $ticket->update([
            'status' => $request->status,
            'priority' => $request->priority,
            'closed_at' => in_array($request->status, ['closed', 'resolved']) ? now() : null,
            'closed_by' => in_array($request->status, ['closed', 'resolved']) ? Auth::id() : null,
        ]);

        if ($oldStatus !== $request->status) {
            $statusLabels = [
                'open' => __('support.status_open'),
                'in_progress' => __('support.status_in_progress'),
                'answered' => __('support.status_answered'),
                'resolved' => __('support.status_resolved'),
                'closed' => __('support.status_closed'),
            ];
            try {
                app(NotificationService::class)->send(
                    $ticket->user_id,
                    __('support.notif_ticket_status', [
                        'number' => $ticket->ticket_number,
                        'status' => $statusLabels[$request->status] ?? $request->status,
                    ]),
                    route('tickets.show', $ticket->ticket_number),
                    'headset'
                );
            } catch (\Throwable $e) {}
        }

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', __('support.admin_ticket_saved'));
    }

    /**
     * Delete a support ticket.
     */
    public function deleteTicket(int $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', __('messages.deleted_successfully') ?? 'Ticket deleted successfully.');
    }

    /* -------------------------------------------------------------------------
     * SUPPORT FORUM MANAGEMENT
     * ------------------------------------------------------------------------- */

    /**
     * List and manage support forum categories.
     */
    public function forumCategories(Request $request)
    {
        $categories = SupportCategory::withCount('topics')->orderBy('sort_order', 'asc')->get();

        return view('admin::admin.support.forum.categories', compact('categories'));
    }

    /**
     * Store new support category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:support_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:30',
            'sort_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        if (empty($slug)) {
            $slug = 'cat-' . time();
        }

        SupportCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'icon' => $request->icon ?: 'fa-solid fa-circle-question',
            'color' => $request->color ?: '#615dfa',
            'sort_order' => (int) $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.support.forum.categories')
            ->with('success', __('support.category_saved'));
    }

    /**
     * Update support category.
     */
    public function updateCategory(Request $request, int $id)
    {
        $category = SupportCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:support_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:30',
            'sort_order' => 'nullable|integer',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $category->update([
            'name' => $request->name,
            'slug' => $slug ?: $category->slug,
            'description' => $request->description,
            'icon' => $request->icon ?: 'fa-solid fa-circle-question',
            'color' => $request->color ?: '#615dfa',
            'sort_order' => (int) $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.support.forum.categories')
            ->with('success', __('support.category_saved'));
    }

    /**
     * Delete support category.
     */
    public function deleteCategory(int $id)
    {
        $category = SupportCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.support.forum.categories')
            ->with('success', __('support.category_deleted'));
    }

    /**
     * List and moderate support topics.
     */
    public function forumTopics(Request $request)
    {
        $query = SupportTopic::with(['category', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('title', 'like', "%{$search}%");
        }

        $topics = $query->paginate(20)->appends($request->query());
        $categories = SupportCategory::orderBy('sort_order', 'asc')->get();

        return view('admin::admin.support.forum.topics', compact('topics', 'categories'));
    }

    /**
     * Toggle pin topic.
     */
    public function togglePinTopic(int $id)
    {
        $topic = SupportTopic::findOrFail($id);
        $topic->update(['is_pinned' => !$topic->is_pinned]);

        return back()->with('success', __('messages.updated_successfully') ?? 'Updated successfully.');
    }

    /**
     * Toggle lock topic.
     */
    public function toggleLockTopic(int $id)
    {
        $topic = SupportTopic::findOrFail($id);
        $topic->update(['is_locked' => !$topic->is_locked]);

        return back()->with('success', __('messages.updated_successfully') ?? 'Updated successfully.');
    }

    /**
     * Delete support topic.
     */
    public function deleteTopic(int $id)
    {
        $topic = SupportTopic::findOrFail($id);
        $category = $topic->category;
        $topic->delete();

        if ($category) {
            $category->update([
                'topics_count' => $category->topics()->count(),
            ]);
        }

        return back()->with('success', __('messages.deleted_successfully') ?? 'Deleted successfully.');
    }
}
