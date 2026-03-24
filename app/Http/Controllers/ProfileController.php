<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\BadgeShowcase;
use App\Models\Directory;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Option;
use App\Models\PointTransaction;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\GamificationService;
use App\Services\StatusActivityService;
use App\Services\UserPrivacyService;
use App\Services\V420SchemaService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request, string $username)
    {
        $viewer = Auth::user();
        $user = User::where('username', $username)->firstOrFail();
        $privacy = app(UserPrivacyService::class);

        abort_unless($privacy->canViewProfile($user, $viewer), 403);

        $cover = $this->resolveCover($user);
        $isFollowing = $viewer
            ? Like::where('uid', $viewer->id)->where('sid', $user->id)->where('type', 1)->exists()
            : false;

        $followersCount = Like::where('sid', $user->id)->where('type', 1)->count();
        $followingCount = Like::where('uid', $user->id)->where('type', 1)->count();
        $postsCount = Status::where('uid', $user->id)->count();
        $selectedTab = (string) $request->query('tab', 'timeline');

        $activityService = app(StatusActivityService::class);
        $hiddenDirectoryStatusIds = $activityService->hiddenDirectoryStatusIds();

        $query = Status::query()
            ->where('uid', $user->id)
            ->where('date', '<', time())
            ->when($selectedTab !== 'links' && !empty($hiddenDirectoryStatusIds), fn ($builder) => $builder->whereNotIn('id', $hiddenDirectoryStatusIds))
            ->orderBy('date', 'desc');

        switch ($selectedTab) {
            case 'blog':
                $query->where('s_type', 100);
                break;
            case 'links':
                $query->where('s_type', 1);
                break;
            case 'forum':
                $query->where('s_type', 2);
                break;
            case 'store':
                $query->where('s_type', 7867);
                break;
            case 'photos':
            case 'about':
                $query->whereRaw('1 = 0');
                break;
        }

        $activities = $query->paginate(10);
        $activityService->decorateMany($activities);

        $photoItems = $selectedTab === 'photos'
            ? $this->paginateCollection($this->photoItemsForUser($user), 18, $request->integer('page', 1))
            : $this->paginateCollection(collect(), 18, 1);

        $schema = app(V420SchemaService::class);
        $badgeShowcase = collect();

        if ($schema->supports('badges')) {
            $badgeShowcase = BadgeShowcase::with('badge')
                ->where('user_id', $user->id)
                ->orderBy('sort_order')
                ->take(6)
                ->get();
        }

        if ($schema->supports('badges') && $badgeShowcase->isEmpty()) {
            $badgeShowcase = UserBadge::with('badge')
                ->where('user_id', $user->id)
                ->whereNotNull('unlocked_at')
                ->orderByDesc('unlocked_at')
                ->take(6)
                ->get()
                ->map(fn ($item) => (object) ['badge' => $item->badge, 'sort_order' => 0]);
        }

        $canViewAbout = $privacy->canViewAbout($user, $viewer);
        $canViewPhotos = $privacy->canViewPhotos($user, $viewer);
        $canViewFollowers = $privacy->canViewFollowers($user, $viewer);
        $canViewFollowing = $privacy->canViewFollowing($user, $viewer);
        $canSendMessage = $viewer && (int) $viewer->id !== (int) $user->id && $privacy->canDirectMessage($user, $viewer);
        $showOnlineStatus = $privacy->shouldShowOnlineStatus($user, $viewer);

        $this->seo([
            'scope_key' => 'profile_show',
            'content_type' => 'user',
            'content_id' => $user->id,
            'resource_title' => $user->username,
            'description' => $canViewAbout && trim((string) $user->sig) !== ''
                ? Str::limit(strip_tags((string) $user->sig), 170, '')
                : __('messages.seo_profile_description', ['username' => $user->username]),
            'image' => $user->img,
            'username' => $user->username,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => $user->username, 'url' => route('profile.show', $user->username)],
            ],
        ]);

        return view('theme::profile.show', compact(
            'user',
            'cover',
            'isFollowing',
            'activities',
            'followersCount',
            'followingCount',
            'postsCount',
            'selectedTab',
            'photoItems',
            'badgeShowcase',
            'canViewAbout',
            'canViewPhotos',
            'canViewFollowers',
            'canViewFollowing',
            'canSendMessage',
            'showOnlineStatus'
        ));
    }

    public function followers(string $username)
    {
        $this->noindex(['scope_key' => 'profile.followers']);

        $viewer = Auth::user();
        $user = User::where('username', $username)->firstOrFail();
        abort_unless(app(UserPrivacyService::class)->canViewFollowers($user, $viewer), 403);

        $followers = Like::where('sid', $user->id)
            ->where('type', 1)
            ->with('user')
            ->paginate(20);

        $cover = $this->resolveCover($user);
        $isFollowing = $viewer
            ? Like::where('uid', $viewer->id)->where('sid', $user->id)->where('type', 1)->exists()
            : false;

        $followersCount = Like::where('sid', $user->id)->where('type', 1)->count();
        $followingCount = Like::where('uid', $user->id)->where('type', 1)->count();
        $postsCount = Status::where('uid', $user->id)->count();

        return view('theme::profile.followers', compact('user', 'followers', 'cover', 'isFollowing', 'followersCount', 'followingCount', 'postsCount'));
    }

    public function following(string $username)
    {
        $this->noindex(['scope_key' => 'profile.following']);

        $viewer = Auth::user();
        $user = User::where('username', $username)->firstOrFail();
        abort_unless(app(UserPrivacyService::class)->canViewFollowing($user, $viewer), 403);

        $following = Like::where('uid', $user->id)
            ->where('type', 1)
            ->with('targetUser')
            ->paginate(20);

        $cover = $this->resolveCover($user);
        $isFollowing = $viewer
            ? Like::where('uid', $viewer->id)->where('sid', $user->id)->where('type', 1)->exists()
            : false;

        $followersCount = Like::where('sid', $user->id)->where('type', 1)->count();
        $followingCount = Like::where('uid', $user->id)->where('type', 1)->count();
        $postsCount = Status::where('uid', $user->id)->count();

        return view('theme::profile.following', compact('user', 'following', 'cover', 'isFollowing', 'followersCount', 'followingCount', 'postsCount'));
    }

    public function showById(int $id)
    {
        $user = User::findOrFail($id);
        $option = Option::where('o_type', 'user')->where('o_order', $user->id)->first();

        if (!$option) {
            Option::create([
                'name' => $user->username,
                'o_valuer' => urlencode(mb_ereg_replace('\s+', '-', $user->username)),
                'o_type' => 'user',
                'o_parent' => 0,
                'o_order' => $user->id,
                'o_mode' => 'upload/cover.jpg',
            ]);
        }

        return redirect()->route('profile.show', $user->username);
    }

    public function toggleFollow(Request $request, int $id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        if ((int) $currentUser->id === (int) $targetUser->id) {
            return back()->with('error', __('cannot_follow_self'));
        }

        $existing = Like::where('uid', $currentUser->id)
            ->where('sid', $targetUser->id)
            ->where('type', 1)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', __('unfollowed_successfully'));
        }

        Like::create([
            'uid' => $currentUser->id,
            'sid' => $targetUser->id,
            'type' => 1,
            'time_t' => time(),
        ]);

        app(GamificationService::class)->refreshBadges($targetUser->id);

        return back()->with('success', __('followed_successfully'));
    }

    public function edit()
    {
        $user = Auth::user();
        $privacySettings = app(UserPrivacyService::class)->settingsFor($user);
        return view('theme::profile.edit', compact('user', 'privacySettings'));
    }

    public function history()
    {
        $user = Auth::user();
        $schema = app(V420SchemaService::class);
        $featureAvailable = $schema->supports('point_history');
        $upgradeNotice = $schema->notice('point_history', __('messages.pts_history'));

        $legacyReferenceIds = [];
        $ledgerHistory = collect();

        if ($featureAvailable) {
            $legacyReferenceIds = PointTransaction::where('user_id', $user->id)
                ->where('reference_type', 'legacy_option')
                ->pluck('reference_id')
                ->filter()
                ->map(static fn ($id) => (int) $id)
                ->all();

            $ledgerHistory = PointTransaction::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->get()
                ->map(function (PointTransaction $item) {
                    return (object) [
                        'id' => $item->id,
                        'amount' => (float) $item->amount,
                        'description_key' => $item->description_key,
                        'created_at_ts' => optional($item->created_at)->timestamp ?? time(),
                        'is_legacy' => false,
                    ];
                });
        }

        $history = $ledgerHistory
            ->merge(
                Option::where('o_type', 'hest_pts')
                    ->where('o_parent', $user->id)
                    ->when(!empty($legacyReferenceIds), fn ($query) => $query->whereNotIn('id', $legacyReferenceIds))
                    ->orderByDesc('id')
                    ->get()
                    ->map(function (Option $item) {
                        return (object) [
                            'id' => $item->id,
                            'amount' => (float) (is_numeric($item->o_valuer) ? $item->o_valuer : 0),
                            'description_key' => $item->name,
                            'created_at_ts' => is_numeric($item->o_mode) ? (int) $item->o_mode : time(),
                            'is_legacy' => true,
                        ];
                    })
            )
            ->sortByDesc('created_at_ts')
            ->values();

        $paginatedHistory = $this->paginateCollection($history, 20, request()->integer('page', 1));

        return view('theme::profile.history', [
            'user' => $user,
            'history' => $paginatedHistory,
            'featureAvailable' => $featureAvailable,
            'upgradeNotice' => $upgradeNotice,
        ]);
    }

    public function privacy()
    {
        $user = Auth::user();
        $schema = app(V420SchemaService::class);
        $privacySettings = app(UserPrivacyService::class)->settingsFor($user);
        $visibilityOptions = app(UserPrivacyService::class)->visibilityOptions();
        $featureAvailable = $schema->supports('privacy');
        $upgradeNotice = $schema->notice('privacy', __('messages.privacy_settings'));

        return view('theme::profile.privacy', compact('user', 'privacySettings', 'visibilityOptions', 'featureAvailable', 'upgradeNotice'));
    }

    public function updatePrivacy(Request $request)
    {
        $schema = app(V420SchemaService::class);
        if (!$schema->supports('privacy')) {
            return redirect()->route('profile.privacy')
                ->with('error', $schema->blockedActionMessage('privacy', __('messages.privacy_settings')));
        }

        $request->validate([
            'profile_visibility' => 'required|in:public,followers,private',
            'about_visibility' => 'required|in:public,followers,private',
            'photos_visibility' => 'required|in:public,followers,private',
            'followers_visibility' => 'required|in:public,followers,private',
            'following_visibility' => 'required|in:public,followers,private',
            'points_history_visibility' => 'required|in:public,followers,private',
            'allow_direct_messages' => 'nullable|boolean',
            'allow_mentions' => 'nullable|boolean',
            'allow_reposts' => 'nullable|boolean',
            'show_online_status' => 'nullable|boolean',
        ]);

        app(UserPrivacyService::class)->updateSettings(Auth::user(), [
            'profile_visibility' => $request->input('profile_visibility'),
            'about_visibility' => $request->input('about_visibility'),
            'photos_visibility' => $request->input('photos_visibility'),
            'followers_visibility' => $request->input('followers_visibility'),
            'following_visibility' => $request->input('following_visibility'),
            'points_history_visibility' => $request->input('points_history_visibility'),
            'allow_direct_messages' => $request->boolean('allow_direct_messages'),
            'allow_mentions' => $request->boolean('allow_mentions'),
            'allow_reposts' => $request->boolean('allow_reposts'),
            'show_online_status' => $request->boolean('show_online_status'),
        ]);

        return redirect()->route('profile.privacy')->with('success', __('messages.privacy_settings_saved'));
    }

    public function badges()
    {
        $user = Auth::user();
        $schema = app(V420SchemaService::class);
        $featureAvailable = $schema->supports('badges');
        $upgradeNotice = $schema->notice('badges', __('messages.badges'));

        $earnedBadges = $featureAvailable
            ? UserBadge::with('badge')
                ->where('user_id', $user->id)
                ->whereNotNull('unlocked_at')
                ->orderByDesc('unlocked_at')
                ->get()
            : collect();

        $showcaseIds = $featureAvailable
            ? BadgeShowcase::where('user_id', $user->id)
                ->orderBy('sort_order')
                ->pluck('badge_id')
                ->all()
            : [];

        return view('theme::profile.badges', compact('user', 'earnedBadges', 'showcaseIds', 'featureAvailable', 'upgradeNotice'));
    }

    public function updateBadges(Request $request)
    {
        $user = Auth::user();
        $schema = app(V420SchemaService::class);
        if (!$schema->supports('badges')) {
            return redirect()->route('profile.badges')
                ->with('error', $schema->blockedActionMessage('badges', __('messages.badges')));
        }

        $badgeIds = collect($request->input('badge_ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(6)
            ->values();

        $ownedBadgeIds = UserBadge::where('user_id', $user->id)
            ->whereNotNull('unlocked_at')
            ->whereIn('badge_id', $badgeIds)
            ->pluck('badge_id')
            ->all();

        BadgeShowcase::where('user_id', $user->id)->delete();
        foreach (array_values($ownedBadgeIds) as $index => $badgeId) {
            BadgeShowcase::create([
                'user_id' => $user->id,
                'badge_id' => $badgeId,
                'sort_order' => $index + 1,
            ]);
        }

        return redirect()->route('profile.badges')->with('success', __('messages.badge_showcase_saved'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:4096',
            'about_me' => 'nullable|string|max:4000',
        ]);

        $user->email = $request->email;
        $user->sig = $request->input('about_me', $user->sig);

        if ($request->filled('password')) {
            $user->pass = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $imageName = time() . '_avatar.' . $request->avatar->extension();
            $request->avatar->move(base_path('upload'), $imageName);
            $user->img = 'upload/' . $imageName;
        }

        if ($request->hasFile('cover')) {
            $coverName = time() . '_cover.' . $request->cover->extension();
            $request->cover->move(base_path('upload'), $coverName);

            $option = Option::where('o_type', 'user')->where('o_order', $user->id)->first();
            if ($option) {
                $option->o_mode = 'upload/' . $coverName;
                $option->save();
            } else {
                Option::create([
                    'name' => $user->username,
                    'o_valuer' => Str::slug($user->username),
                    'o_type' => 'user',
                    'o_parent' => 0,
                    'o_order' => $user->id,
                    'o_mode' => 'upload/' . $coverName,
                ]);
            }
        }

        $user->save();
        app(GamificationService::class)->refreshBadges($user->id);

        return redirect()->route('profile.edit')->with('success', __('profile_updated_successfully'));
    }

    private function resolveCover(User $user): string
    {
        $coverOption = Option::where('o_type', 'user')->where('o_order', $user->id)->first();
        $cover = $coverOption ? $coverOption->o_mode : 'upload/cover.jpg';
        return $cover === '0' ? 'upload/cover.jpg' : $cover;
    }

    private function photoItemsForUser(User $user): Collection
    {
        $items = collect();
        $statuses = Status::where('uid', $user->id)
            ->where('s_type', 4)
            ->orderByDesc('date')
            ->get();

        foreach ($statuses as $status) {
            $topic = ForumTopic::with(['attachments', 'imageOption'])->find($status->tp_id);
            if (!$topic) {
                continue;
            }

            $images = collect();
            foreach ($topic->attachments as $attachment) {
                if ($attachment->isImage()) {
                    $images->push($attachment->file_path);
                }
            }

            if ($images->isEmpty() && $topic->image_url) {
                $images->push($topic->image_url);
            }

            foreach ($images as $index => $imagePath) {
                $items->push((object) [
                    'image_url' => asset($imagePath),
                    'post_url' => route('forum.topic', $topic->id),
                    'caption' => Str::limit(strip_tags((string) $topic->txt), 80),
                    'timestamp' => (int) $status->date,
                    'key' => $status->id . ':' . $index,
                ]);
            }
        }

        return $items->sortByDesc('timestamp')->values();
    }

    private function paginateCollection(Collection $items, int $perPage, int $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
