<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store', 'update', 'destroy'],
        ]);
    }

    public function index(): AnonymousResourceCollection
    {

        $articles = Article::query()
            ->allowedIncludes(['category', 'author', 'comments'])
            ->allowedFilters(['title', 'content', 'month', 'year', 'categories'])
            ->allowedSorts(['title', 'content'])
            ->sparseFieldset()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    public function show($idArticle): JsonResource
    {
        $article = Article::where('id', $idArticle)
            ->allowedIncludes(['category', 'author', 'comments'])
            ->sparseFieldset()
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function store(SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('create', new Article);

        $articleData = $request->getAttributes();
        $articleData['user_id'] = $request->getRelationshipId('author');

        $categorySlug = $request->getRelationshipId('category');
        $category = Category::where('slug', $categorySlug)->first();
        $articleData['category_id'] = $category->id;

        $article = Article::create($articleData);

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {
        $this->authorize('update', $article);
        // Validacion del objeto
        $articleData = $request->getAttributes();

        if ($request->hasRelationship('author')) {
            $articleData['user_id'] = $request->getRelationshipId('author');
        }

        if ($request->hasRelationship('category')) {
            $categorySlug = $request->getRelationshipId('category');
            $category = Category::where('slug', $categorySlug)->first();
            $articleData['category_id'] = $category->id;
        }

        $article->update($articleData);

        return ArticleResource::make($article);
    }

    public function destroy(Article $article, Request $request)
    {

        $this->authorize('delete', $article);

        $article->delete();

        return response()->noContent();
    }
}
