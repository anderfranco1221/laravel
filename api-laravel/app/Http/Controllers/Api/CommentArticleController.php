<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;

class CommentArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Comment $comment): array
    {
        return ArticleResource::identifier($comment->article);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment): ArticleResource
    {
        return ArticleResource::make($comment->article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Comment $comment, Request $request): array
    {
        $request->validate(['data.id' => ['exists:articles,id']]);

        $articleId = $request->input('data.id');
        $article = Article::findOrFail($articleId);

        $comment->update(['article_id' => $article->id]);

        return ArticleResource::identifier($article);
    }
}
