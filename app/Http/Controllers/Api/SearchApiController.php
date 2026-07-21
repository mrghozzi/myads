<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Status;
use App\Models\ForumTopic;
use App\Models\Product;

class SearchApiController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (empty(trim($query)) || strlen($query) < 2) {
            return response()->json(['success' => true, 'data' => []]);
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
                    'id' => $user->id,
                    'identifier' => $user->username,
                    'title' => $user->username,
                    'img' => $user->avatarUrl(),
                    'subtitle' => '@' . $user->username,
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
                    'id' => $product->id,
                    'title' => $product->name,
                    'img' => $product->product_image ? url($product->product_image) : url('themes/default/assets/img/error_plug.png'),
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
                        'id' => $topic->id,
                        'title' => $topic->name,
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
                    'id' => $status->id,
                    'title' => __('messages.post') . ' by ' . ($status->user->username ?? 'User'),
                    'img' => $status->user ? $status->user->avatarUrl() : url('upload/_avatar.png'),
                    'subtitle' => \Illuminate\Support\Str::limit(strip_tags($status->txt), 40)
                ];
            }

        } catch (\Throwable $e) {
            \Log::error('Live API search error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => __('messages.error_occurred')], 500);
        }

        return response()->json(['success' => true, 'data' => $results]);
    }
}
