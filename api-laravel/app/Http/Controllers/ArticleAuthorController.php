<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResources;

class ArticleAuthorController extends Controller
{
    public function index(Article $article)
    {
        return AuthorResources::identifier($article->author);
    }

    public function show(Article $article)
    {
        return AuthorResources::make($article->author);
    }

    public function update(Article $article, Request $request)
    {
        $request->validate([
            'data.id' => ['required', 'exists:users,id'],
        ]);

        $article->update(['user_id' => $request->input('data.id')]);

        return AuthorResources::identifier($article->author);
    }
}
