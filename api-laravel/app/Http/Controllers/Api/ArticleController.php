<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleController extends Controller
{
    public function index(): AnonymousResourceCollection{

        $articles = Article::query()
                ->allowedIncludes(["category", "author"])
                ->allowedFilters(['title', 'content', 'month', 'year', 'categories'])
                ->allowedSorts(['title', 'content'])
                ->sparseFieldset()
                ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    public function show($idArticle): JsonResource{
        $article = Article::where('id', $idArticle)
        ->allowedIncludes(["category", "author"])
        ->sparseFieldset()
        ->firstOrFail();

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
