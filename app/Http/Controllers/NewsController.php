<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('id', 'desc')->paginate(10);
        return view('theme::news.index', compact('news'));
    }

    public function show($id)
    {
        $article = News::findOrFail($id);
        return view('theme::news.show', compact('article'));
    }
}
