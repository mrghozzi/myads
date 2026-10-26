<?php

namespace App\Http\Controllers;

use App\Models\Option;
use App\Models\SupportCategory;
use App\Models\SupportTopic;
use App\Models\SupportReply;
use App\Services\AdminAccessService;
use App\Services\NotificationService;
use App\Services\SeoManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportForumController extends Controller
{
    protected function getSiteName(): string
    {
        return Option::where('name', 'titer')->value('o_valuer')
            ?? config('app.name', 'MyAds');
    }

    /**
     * Display the support forum homepage.
     */
    public function index(Request $request)
    {
        $siteName = $this->getSiteName();
        $categories = SupportCategory::active()->withCount('topics')->get();

        $filter = $request->query('filter', 'all'); // all, solved, unsolved
        $search = trim((string) $request->query('q', ''));

        $query = SupportTopic::with(['category', 'user', 'bestReply'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');

        if ($filter === 'solved') {
            $query->where('status', 'resolved');
        } elseif ($filter === 'unsolved') {
            $query->where('status', '!=', 'resolved');
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $topics = $query->paginate(15)->appends($request->query());

        $totalTopics = SupportTopic::count();
        $totalSolved = SupportTopic::where('status', 'resolved')->count();
        $totalCategories = $categories->count();

        $pageTitle = __('support.support_forum') . ' - ' . $siteName;
        $seo = app(SeoManager::class)->resolve($request);

        return view('theme::support.index', compact(
            'siteName',
            'categories',
            'topics',
            'filter',
            'search',
            'totalTopics',
            'totalSolved',
            'totalCategories',
            'pageTitle',
            'seo'
        ));
    }

    /**
     * Display a specific support category.
     */
    public function category(Request $request, string $slug)
    {
        $siteName = $this->getSiteName();
        $category = SupportCategory::where('slug', $slug)->firstOrFail();
        $categories = SupportCategory::active()->withCount('topics')->get();

        $filter = $request->query('filter', 'all');
        $query = $category->topics()->with(['user', 'bestReply']);

        if ($filter === 'solved') {
            $query->where('status', 'resolved');
        } elseif ($filter === 'unsolved') {
            $query->where('status', '!=', 'resolved');
        }

        $topics = $query->paginate(15)->appends($request->query());

        $pageTitle = $category->name . ' - ' . __('support.support_forum') . ' - ' . $siteName;
        $seo = app(SeoManager::class)->resolve($request);

        return view('theme::support.category', compact(
            'siteName',
            'category',
            'categories',
            'topics',
            'filter',
            'pageTitle',
            'seo'
        ));
    }

    /**
     * Show form to ask a question.
     */
    public function create(Request $request)
    {
        $siteName = $this->getSiteName();
        $categories = SupportCategory::active()->get();
        $selectedCategoryId = (int) $request->query('category_id', 0);

        $pageTitle = __('support.ask_question') . ' - ' . $siteName;
        $seo = app(SeoManager::class)->resolve($request);

        return view('theme::support.create', compact(
            'siteName',
            'categories',
            'selectedCategoryId',
            'pageTitle',
            'seo'
        ));
    }

    /**
     * Store a new support question.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:support_categories,id',
            'title' => 'required|string|min:5|max:200',
            'content' => 'required|string|min:10',
        ]);

        $title = trim($request->title);
        $slug = Str::slug($title);
        if (empty($slug)) {
            $slug = 'question-' . time();
        }

        $topic = SupportTopic::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $title,
            'slug' => $slug,
            'content' => $request->content,
            'status' => 'open',
            'is_pinned' => false,
            'is_locked' => false,
            'views_count' => 0,
            'replies_count' => 0,
        ]);

        $topic->category()->increment('topics_count');

        return redirect()->route('support.topic', ['id' => $topic->id, 'slug' => $topic->slug])
            ->with('success', __('support.topic_created_success'));
    }

    /**
     * Show a support topic and its replies.
     */
    public function show(Request $request, int $id, ?string $slug = null)
    {
        $siteName = $this->getSiteName();
        $topic = SupportTopic::with(['category', 'user', 'replies.user'])->findOrFail($id);

        $topic->increment('views_count');

        $relatedTopics = SupportTopic::where('category_id', $topic->category_id)
            ->where('id', '!=', $topic->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $isStaff = Auth::check() && (
            Auth::user()->isSuperAdmin() ||
            app(AdminAccessService::class)->canAccess(Auth::user())
        );

        $pageTitle = $topic->title . ' - ' . __('support.support_forum') . ' - ' . $siteName;
        $seo = app(SeoManager::class)->resolve($request);

        return view('theme::support.show', compact(
            'siteName',
            'topic',
            'relatedTopics',
            'isStaff',
            'pageTitle',
            'seo'
        ));
    }

    /**
     * Add a reply to a support topic.
     */
    public function reply(Request $request, int $id)
    {
        $request->validate([
            'content' => 'required|string|min:3',
        ]);

        $topic = SupportTopic::findOrFail($id);

        if ($topic->is_locked) {
            return back()->with('error', __('support.locked'));
        }

        $isStaff = Auth::user()->isSuperAdmin() ||
            app(AdminAccessService::class)->canAccess(Auth::user());

        $reply = SupportReply::create([
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'is_staff' => $isStaff,
            'is_solution' => false,
        ]);

        $topic->update([
            'replies_count' => $topic->replies()->count(),
            'last_reply_at' => now(),
            'last_reply_user_id' => Auth::id(),
        ]);

        $topic->category()->increment('replies_count');

        // Notify topic author if replier is someone else
        if ($topic->user_id !== Auth::id()) {
            app(NotificationService::class)->send(
                $topic->user_id,
                __('support.notif_forum_reply', ['title' => Str::limit($topic->title, 40)]),
                route('support.topic', ['id' => $topic->id, 'slug' => $topic->slug]) . '#reply-' . $reply->id,
                'comments'
            );
        }

        return redirect()->route('support.topic', ['id' => $topic->id, 'slug' => $topic->slug])
            ->with('success', __('support.reply_added_success'));
    }

    /**
     * Mark or unmark a reply as official solution.
     */
    public function markSolution(Request $request, int $topicId, int $replyId)
    {
        $topic = SupportTopic::findOrFail($topicId);
        $reply = SupportReply::where('topic_id', $topic->id)->findOrFail($replyId);

        $canMark = Auth::id() === $topic->user_id ||
            Auth::user()->isSuperAdmin() ||
            app(AdminAccessService::class)->canAccess(Auth::user());

        if (!$canMark) {
            abort(403);
        }

        if ($reply->is_solution) {
            $reply->update(['is_solution' => false]);
            $topic->update([
                'best_reply_id' => null,
                'status' => 'open',
            ]);
            $message = __('support.unmark_solution');
        } else {
            SupportReply::where('topic_id', $topic->id)->update(['is_solution' => false]);
            $reply->update(['is_solution' => true]);
            $topic->update([
                'best_reply_id' => $reply->id,
                'status' => 'resolved',
            ]);

            // Notify reply author if not self
            if ($reply->user_id !== Auth::id()) {
                app(NotificationService::class)->send(
                    $reply->user_id,
                    __('support.notif_forum_solution'),
                    route('support.topic', ['id' => $topic->id, 'slug' => $topic->slug]) . '#reply-' . $reply->id,
                    'award'
                );
            }

            $message = __('support.solution_marked_success');
        }

        return redirect()->route('support.topic', ['id' => $topic->id, 'slug' => $topic->slug])
            ->with('success', $message);
    }
}
