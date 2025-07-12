<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\AuthorResources;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends Controller
{
    public function show($author): JsonResource
    {
        $author = User::findOrFail($author);

        return AuthorResources::make($author);
    }

    public function index(): AnonymousResourceCollection
    {
        $authors = User::jsonPaginate();

        return AuthorResources::collection($authors);
    }
}
