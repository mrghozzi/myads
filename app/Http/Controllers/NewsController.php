<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\News;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = News::orderBy('id', 'desc')->paginate(10);

        if ($request->boolean('ajax') || $request->ajax() || $request->wantsJson()) {
            $items = $news->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'name' => (string) $item->name,
                    'date' => $item->date ? date('Y-m-d', (int) $item->date) : '',
                    'excerpt' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $item->text))), 230),
                ];
            })->values();

            return response()->json([
                'items' => $items,
                'has_more' => $news->hasMorePages(),
                'next_page' => $news->hasMorePages() ? ($news->currentPage() + 1) : null,
            ]);
        }

        return view('theme::news.index', compact('news'));
    }

    public function show($id)
    {
        $article = News::findOrFail($id);
        return view('theme::news.show', compact('article'));
    }
}
