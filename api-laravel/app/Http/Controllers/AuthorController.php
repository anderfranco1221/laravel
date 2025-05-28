<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

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
