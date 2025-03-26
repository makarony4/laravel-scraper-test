<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class IndexPageController
{
    public function index(Request $request)
    {

        $articles = Article::all();
        return view('index', compact('articles'));
    }
}
