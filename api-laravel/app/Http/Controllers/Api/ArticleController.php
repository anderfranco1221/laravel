<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;

class ArticleController extends Controller
{
    public function index():ArticleCollection{
        
        return ArticleCollection::make(Article::all());
    }

    public function show(Article $article): ArticleResource{
        //dd($article->toArray());
        return ArticleResource::make($article);
    }

    public function store(SaveArticleRequest $request): ArticleResource{
       
        $article = Article::create($request->validated());

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {

        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article){
        $article->delete();

        return response()->noContent();
    }
}
