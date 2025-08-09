<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResources;

class CommentAuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Comment $comment): array
    {
        return AuthorResources::identifier($comment->author);
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
    public function show(Comment $comment): AuthorResources
    {
        return AuthorResources::make($comment->author);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Comment $comment, Request $request): array
    {
        $request->validate([
            'data.id' => ['required', 'exists:users,id'],
        ]);

        $userId = $request->input('data.id');

        $comment->update(['user_id' => $userId]);

        return AuthorResources::identifier($comment->author);
    }
}
