<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Option;
use App\Models\Like;
use App\Models\Status;
use App\Models\ForumTopic;
use App\Models\Directory;
use App\Models\Product;

class ProfileController extends Controller
{
    public function show(Request $request, $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        // Fetch Cover Photo from Options
        $coverOption = Option::where('o_type', 'user')->where('o_order', $user->id)->first();
        $cover = $coverOption ? $coverOption->o_mode : 'upload/cover.jpg';
        // Handle cover if it's "0"
        if ($cover === '0') $cover = 'upload/cover.jpg';
        
        // Check Follow Status
        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = Like::where('uid', Auth::id())
                ->where('sid', $user->id)
                ->where('type', 1)
                ->exists();
        }

        // Stats
        $followersCount = Like::where('sid', $user->id)->where('type', 1)->count();
        $followingCount = Like::where('uid', $user->id)->where('type', 1)->count();
        $postsCount = ForumTopic::where('uid', $user->id)->count();

        // Activities
        $query = Status::where('uid', $user->id)
            ->where('date', '<', time())
            ->orderBy('date', 'desc');

        if ($request->has('tab')) {
            switch ($request->tab) {
                case 'blog':
                    $query->where('s_type', 100);
                    break;
                case 'links':
                    $query->where('s_type', 1);
                    break;
                case 'photos':
                    $query->where('s_type', 4);
                    break;
                case 'forum':
                    $query->where('s_type', 2);
                    break;
                case 'store':
                    $query->where('s_type', 7867);
                    break;
            }
        }

        $activities = $query->paginate(10);

        foreach ($activities as $activity) {
            $activity->related_content = null;
            
            switch ($activity->s_type) {
                case 1: // Directory
                    $activity->related_content = Directory::find($activity->tp_id);
                    $activity->type_label = 'Directory';
                    break;
                case 2: // Forum Topic
                case 100: // Forum Text Post (Legacy)
                case 4: // Forum Image
                    $activity->related_content = ForumTopic::find($activity->tp_id);
                    $activity->type_label = 'Forum';
                    break;
                case 7867: // Store Product
                    $activity->related_content = Product::withoutGlobalScope('store')->find($activity->tp_id);
                    $activity->type_label = 'Store';
                    break;
            }
        }

        if ($request->ajax()) {
            $html = view('theme::partials.ajax.activities', compact('activities'))->render();
            return response()->json([
                'html' => $html,
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        $this->seo([
            'scope_key' => 'profile_show',
            'content_type' => 'user',
            'content_id' => $user->id,
            'resource_title' => $user->username,
            'description' => Str::limit(strip_tags((string) $user->sig), 170, '') ?: __('messages.seo_profile_description', ['username' => $user->username]),
            'image' => $user->img,
            'username' => $user->username,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => $user->username, 'url' => route('profile.show', $user->username)],
            ],
        ]);

        return view('theme::profile.show', compact('user', 'cover', 'isFollowing', 'activities', 'followersCount', 'followingCount', 'postsCount'));
    }

    public function followers($username)
    {
        $this->noindex([
            'scope_key' => 'profile.followers',
        ]);

        $user = User::where('username', $username)->firstOrFail();
        $followers = Like::where('sid', $user->id)
            ->where('type', 1)
            ->with('user')
            ->paginate(20);
            
        // Fetch Cover Photo from Options
        $coverOption = Option::where('o_type', 'user')->where('o_order', $user->id)->first();
        $cover = $coverOption ? $coverOption->o_mode : 'upload/cover.jpg';
        if ($cover === '0') $cover = 'upload/cover.jpg';

        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = Like::where('uid', Auth::id())
                ->where('sid', $user->id)
                ->where('type', 1)
                ->exists();
        }
        
        $followersCount = Like::where('sid', $user->id)->where('type', 1)->count();
        $followingCount = Like::where('uid', $user->id)->where('type', 1)->count();
        $postsCount = ForumTopic::where('uid', $user->id)->count();

        return view('theme::profile.followers', compact('user', 'followers', 'cover', 'isFollowing', 'followersCount', 'followingCount', 'postsCount'));
    }

    public function following($username)
    {
        $this->noindex([
            'scope_key' => 'profile.following',
        ]);

        $user = User::where('username', $username)->firstOrFail();
        $following = Like::where('uid', $user->id)
            ->where('type', 1)
            ->with('targetUser')
            ->paginate(20);

        // Fetch Cover Photo from Options
        $coverOption = Option::where('o_type', 'user')->where('o_order', $user->id)->first();
        $cover = $coverOption ? $coverOption->o_mode : 'upload/cover.jpg';
        if ($cover === '0') $cover = 'upload/cover.jpg';

        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = Like::where('uid', Auth::id())
                ->where('sid', $user->id)
                ->where('type', 1)
                ->exists();
        }
        
        $followersCount = Like::where('sid', $user->id)->where('type', 1)->count();
        $followingCount = Like::where('uid', $user->id)->where('type', 1)->count();
        $postsCount = ForumTopic::where('uid', $user->id)->count();

        return view('theme::profile.following', compact('user', 'following', 'cover', 'isFollowing', 'followersCount', 'followingCount', 'postsCount'));
    }

    public function showById($id)
    {
        $user = User::findOrFail($id);
        
        // Ensure Option entry exists (legacy compatibility)
        $option = Option::where('o_type', 'user')->where('o_order', $user->id)->first();
        if (!$option) {
            $slug = urlencode(mb_ereg_replace('\s+', '-', $user->username));
            $option = new Option();
            $option->name = $user->username;
            $option->o_valuer = $slug;
            $option->o_type = 'user';
            $option->o_parent = 0; // or whatever default
            $option->o_order = $user->id;
            $option->o_mode = 'upload/cover.jpg';
            $option->save();
        }

        return redirect()->route('profile.show', $user->username);
    }

    public function toggleFollow(Request $request, $id)
    {
        $targetUser = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->id == $targetUser->id) {
            return back()->with('error', __('cannot_follow_self'));
        }

        $existing = Like::where('uid', $currentUser->id)
            ->where('sid', $targetUser->id)
            ->where('type', 1)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', __('unfollowed_successfully'));
        } else {
            $followedAt = time();

            Like::create([
                'uid' => $currentUser->id,
                'sid' => $targetUser->id,
                'type' => 1,
                'time_t' => $followedAt,
            ]);
            return back()->with('success', __('followed_successfully'));
        }
    }

    public function edit()
    {
        $user = Auth::user();
        return view('theme::profile.edit', compact('user'));
    }

    public function history()
    {
        $user = Auth::user();
        $history = Option::where('o_type', 'hest_pts')
            ->where('o_parent', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('theme::profile.history', compact('user', 'history'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:4096',
        ]);

        $user->email = $request->email;

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
            
            // Update Cover in Options
            $option = Option::where('o_type', 'user')->where('o_order', $user->id)->first();
            if ($option) {
                $option->o_mode = 'upload/' . $coverName;
                $option->save();
            } else {
                // Should exist but just in case
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

        return redirect()->route('profile.edit')->with('success', __('profile_updated_successfully'));
    }
}
