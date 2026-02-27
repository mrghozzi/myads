<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumComment;
use App\Models\Status;
use App\Models\Emoji;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::orderBy('ordercat', 'desc')->get();
        return view('theme::forum.index', compact('categories'));
    }

    public function category($id)
    {
        $category = ForumCategory::findOrFail($id);
        $time = time();

        $statuses = Status::where('s_type', 2)
            ->where('date', '<=', $time)
            ->whereIn('tp_id', function ($query) use ($id) {
                $query->select('id')
                    ->from('forum')
                    ->where('cat', $id)
                    ->where('statu', 1);
            })
            ->orderBy('id', 'desc')
            ->paginate(21);

        $topicIds = $statuses->pluck('tp_id');
        $topics = ForumTopic::with(['user', 'category'])
            ->whereIn('id', $topicIds)
            ->get()
            ->keyBy('id');

        if (request()->ajax()) {
            $html = view('theme::partials.ajax.forum_topics', compact('statuses', 'topics'))->render();
            return response()->json([
                'html' => $html,
                'next_page_url' => $statuses->nextPageUrl()
            ]);
        }

        return view('theme::forum.category', compact('category', 'statuses', 'topics'));
    }

    public function topic($id)
    {
        // $id is the topic ID
        $topic = ForumTopic::with(['user', 'comments.user'])->findOrFail($id);
        
        // Check Status table to get s_type (2, 4, 100, 7867)
        $status = \App\Models\Status::where('tp_id', $id)
                    ->whereIn('s_type', [2, 4, 100, 7867])
                    ->firstOrFail();

        // Redirect if Store Product (7867)
        if ($status->s_type == 7867) {
            $product = \App\Models\Product::withoutGlobalScope('store')->find($id);
            if ($product) {
                return redirect()->route('store.show', $product->name);
            }
        }

        // Increment views
        // $topic->increment('views'); // If 'views' column exists

        if ($status->s_type == 100) {
            return view('theme::forum.post', compact('topic', 'status'));
        } elseif ($status->s_type == 4) {
            return view('theme::forum.image', compact('topic', 'status'));
        }

        return view('theme::forum.topic', compact('topic', 'status'));
    }

    public function create(Request $request)
    {
        if (!auth()->check()) {
            abort(404);
        }

        $categories = ForumCategory::orderBy('ordercat', 'asc')->get();
        $emojis = Emoji::orderBy('id', 'asc')->get();
        $topic = null;
        $editType = null;

        if ($request->filled('e')) {
            $topic = ForumTopic::findOrFail($request->input('e'));

            if ($topic->uid != auth()->id() && !auth()->user()->isAdmin()) {
                abort(403);
            }

            $status = Status::where('tp_id', $topic->id)->whereIn('s_type', [2, 7867])->first();
            $editType = $status?->s_type ?? 2;
        }

        return view('theme::forum.create', compact('categories', 'emojis', 'topic', 'editType'));
    }

    public function store(Request $request)
    {
        if (!$request->has('cat') && $request->has('categ')) {
            $request->merge(['cat' => $request->input('categ')]);
        }

        if (!$request->has('type')) {
            $request->merge(['type' => 100]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'txt' => 'required|string',
            'cat' => 'required|integer',
            'type' => 'required|in:100,4', // 100=Post, 4=Image
            'img' => 'nullable|image|max:2048',
        ]);

        $uid = auth()->id();
        $time = time();
        $statu = 1;

        // Image handling
        $imagePath = null;
        if ($request->type == 4 && $request->hasFile('img')) {
            $file = $request->file('img');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('upload'), $filename);
            $imagePath = 'upload/' . $filename;
        }

        $topic = ForumTopic::create([
            'uid' => $uid,
            'name' => $request->name,
            'txt' => $request->txt,
            'cat' => $request->cat,
            'statu' => $statu,
        ]);

        $status = \App\Models\Status::create([
            'uid' => $uid,
            'date' => $time,
            's_type' => $request->type == 100 ? 2 : 4, // 2 for topic, 4 for image post? Check old code.
            // Old code: s_type=2 for topic, s_type=4 for image.
            // Wait, old code says: if($catuss['s_type']==2) $s_type ="forum";
            // And in create post logic: s_type=2 (topic)
            // But if image upload: s_type=4.
            // Let's stick to 2 for standard topics and 4 for image topics.
            'tp_id' => $topic->id,
        ]);
        
        // If image, store in options
        if ($imagePath) {
             \App\Models\Option::create([
                'name' => $time,
                'o_valuer' => $imagePath,
                'o_type' => 'image_post',
                'o_parent' => $topic->id,
                'o_order' => $uid,
                'o_mode' => 'file',
            ]);
            
            // Update status type to 4
            $status->update(['s_type' => 4]);
        } else {
            // Standard topic is type 2
             $status->update(['s_type' => 2]);
        }

        return redirect()->route('forum.topic', $topic->id);
    }

    public function edit($id)
    {
        $topic = ForumTopic::findOrFail($id);
        
        if ($topic->uid != auth()->id() && !auth()->user()->isAdmin()) {
            return abort(403);
        }

        $categories = ForumCategory::orderBy('ordercat', 'asc')->get();
        return view('theme::forum.edit', compact('topic', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $topic = ForumTopic::findOrFail($id);
        
        if ($topic->uid != auth()->id() && !auth()->user()->isAdmin()) {
            return abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'txt' => 'required|string',
            'cat' => 'required|integer',
        ]);

        $topic->update([
            'name' => $request->name,
            'txt' => $request->txt,
            'cat' => $request->cat,
        ]);

        return redirect()->route('forum.topic', $topic->id);
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        if (!$id) return response()->json(['error' => 'Missing ID'], 400);

        $topic = ForumTopic::find($id);
        if (!$topic) return response()->json(['error' => 'Not found'], 404);

        if ($topic->uid != auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete related status
        \App\Models\Status::where('tp_id', $id)->whereIn('s_type', [2, 4, 100, 7867])->delete();
        
        // Delete related options (images, comments if any stored as options)
        \App\Models\Option::where('o_parent', $id)->whereIn('o_type', ['image_post', 'data_reaction'])->delete();

        // Delete comments
        ForumComment::where('tid', $id)->delete();

        // Delete topic
        $topic->delete();

        // If request expects JSON (AJAX or fetch)
    if ($request->wantsJson() || $request->ajax()) {
        return response()->json(['success' => true]);
    }
        
        return redirect()->route('forum.index');
    }
}
