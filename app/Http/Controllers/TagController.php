<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use App\Models\Status;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource filtered by tag.
     *
     * @param string $tag
     * @return \Illuminate\View\View
     */
    public function index(string $tag)
    {
        $tag = ltrim($tag, '#');
        $query = '#' . $tag;

        $topics = ForumTopic::visible()
            ->where('txt', 'LIKE', "%{$query}%")
            ->orderBy('date', 'desc')
            ->paginate(15, ['*'], 'topics_page');

        $statuses = Status::visible()
            ->where(function ($q) use ($query) {
                $q->where('txt', 'LIKE', "%{$query}%")
                  ->orWhere('statu', 'LIKE', "%{$query}%");
            })
            ->orderBy('date', 'desc')
            ->paginate(15, ['*'], 'statuses_page');

        $this->seo([
            'scope_key' => 'tag_show',
            'resource_title' => '#' . $tag,
            'description' => __('messages.seo_tag_description', ['tag' => $tag]),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.tag_o'), 'url' => 'javascript:void(0);'],
                ['name' => '#' . $tag, 'url' => route('tag.show', $tag)],
            ],
        ]);

        return view('theme::tag', compact('tag', 'topics', 'statuses'));
    }
}
