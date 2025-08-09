<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Requests\SaveCommentsRequest;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store', 'update', 'destroy'],
        ]);
    }

    public function index()
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveCommentsRequest $request)
    {
        $attributes = $request->getAttributes();
        $comment = new Comment;

        $comment->body = $attributes['body'];
        $comment->user_id = $request->getRelationshipId('author');
        $articleSlug = $request->getRelationshipId('article');
        $comment->article_id = Article::where('id', $articleSlug)->firstOrFail()->id;
        $comment->save();

        return CommentResource::make($comment);
    }

    public function show(Comment $comment)
    {
        return CommentResource::make($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(SaveCommentsRequest $request, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);
        $comment->body = $request->input('data.attributes.body');

        if ($request->hasRelationships('article')) {
            $articleSlug = $request->getRelationshipId('article');
            $comment->article_id = Article::where('id', $articleSlug)->firstOrFail()->id;
        }

        if ($request->hasRelationships('author')) {
            $comment->user_id = $request->getRelationshipId('author');
        }

        $comment->save();

        return CommentResource::make($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
