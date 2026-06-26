<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Status;
use App\Models\ForumTopic;
use App\Models\Product;

class SearchLiveController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (empty(trim($query)) || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        try {
            // Search Users
            $users = User::where('username', 'LIKE', "%{$query}%")
                ->select('id', 'username', 'img')
                ->limit(3)
                ->get();
            
            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'title' => $user->username,
                    'url' => route('profile.show', $user->username),
                    'img' => $user->avatarUrl()
                ];
            }

            // Search Products
            $products = Product::visible()
                ->whereRaw("MATCH(name, o_valuer) AGAINST(? IN BOOLEAN MODE)", [$query])
                ->select('id', 'name', 'product_image', 'o_valuer')
                ->limit(3)
                ->get();

            foreach ($products as $product) {
                $results[] = [
                    'type' => 'product',
                    'title' => $product->name,
                    'url' => route('store.show', $product->name),
                    'img' => $product->product_image ? url($product->product_image) : asset('themes/default/assets/img/error_plug.png'),
                    'subtitle' => \Illuminate\Support\Str::limit(strip_tags($product->o_valuer), 40)
                ];
            }

            // Search Forum Topics
            $topicIds = ForumTopic::visible()
                ->whereRaw("MATCH(name, txt) AGAINST(? IN BOOLEAN MODE)", [$query])
                ->limit(3)
                ->pluck('id');

            if ($topicIds->count() > 0) {
                $topics = ForumTopic::whereIn('id', $topicIds)->get();
                foreach ($topics as $topic) {
                    $results[] = [
                        'type' => 'forum',
                        'title' => $topic->name,
                        'url' => route('forum.topic', $topic->id),
                        'img' => null,
                        'subtitle' => \Illuminate\Support\Str::limit(strip_tags($topic->txt), 40)
                    ];
                }
            }
            
            // Search Posts (Statuses)
            $statuses = Status::visible()
                ->where('s_type', 4) // Type text post
                ->where('txt', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get();
                
            foreach ($statuses as $status) {
                $results[] = [
                    'type' => 'post',
                    'title' => __('messages.post') . ' by ' . ($status->user->username ?? 'User'),
                    'url' => route('portal.index', ['search' => $query]),
                    'img' => $status->user ? $status->user->avatarUrl() : asset('upload/_avatar.png'),
                    'subtitle' => \Illuminate\Support\Str::limit(strip_tags($status->txt), 40)
                ];
            }

        } catch (\Throwable $e) {
            \Log::error('Live search error: ' . $e->getMessage());
        }

        return response()->json(['results' => $results]);
    }
}
