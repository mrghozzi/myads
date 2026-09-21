<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use App\Models\Status;
use App\Services\StatusActivityService;
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

        app(StatusActivityService::class)->decorateMany($statuses);

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

    /**
     * Return matching hashtags for autocomplete.
     */
    public function suggest(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $cleanTag = ltrim($q, '#');

        if ($cleanTag === '') {
            return response()->json([]);
        }

        $cacheKey = 'hashtag_suggestions_' . md5(mb_strtolower($cleanTag));
        $tags = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($cleanTag) {
            $searchPattern = '#' . $cleanTag;
            $statuses = Status::visible()
                ->where('txt', 'LIKE', "%{$searchPattern}%")
                ->latest('id')
                ->limit(50)
                ->pluck('txt');

            $matches = [];
            foreach ($statuses as $text) {
                if (preg_match_all('/#([\p{L}\p{N}_]+)/u', (string) $text, $m)) {
                    foreach ($m[1] as $tag) {
                        if (stripos($tag, $cleanTag) === 0 || mb_stripos($tag, $cleanTag) === 0) {
                            $matches[$tag] = ($matches[$tag] ?? 0) + 1;
                        }
                    }
                }
            }

            arsort($matches);
            $results = [];
            foreach (array_slice(array_keys($matches), 0, 8) as $tag) {
                $results[] = [
                    'tag' => $tag,
                    'display' => '#' . $tag,
                    'count' => $matches[$tag],
                    'url' => route('tag.show', $tag),
                ];
            }

            if (empty($results) && mb_strlen($cleanTag) >= 2) {
                $results[] = [
                    'tag' => $cleanTag,
                    'display' => '#' . $cleanTag,
                    'count' => 1,
                    'url' => route('tag.show', $cleanTag),
                ];
            }

            return $results;
        });

        return response()->json($tags);
    }
}

