<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\Api\LogginController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\ArticleAuthorController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Middleware\ValidateJsonApiDocument;

/*
Route::bind('article', function($article){
    return \App\Models\Article::where('slug', $article)
    ->sparseFieldset()
    ->firstOrFail();
});*/

//Route::name('api.v1.', function(){

Route::apiResource('articles', ArticleController::class);
//    ->names('api.v1.articles');

Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');

Route::apiResource('author', AuthorController::class)
    ->only('index', 'show');

Route::get('articles/{article}/relationships/category', [ArticleCategoryController::class, "index"])
    ->name('articles.relationships.category');

Route::patch('articles/{article}/relationships/category', [ArticleCategoryController::class, "update"])
    ->name('articles.relationships.category');

Route::get('articles/{article}/category', [ArticleCategoryController::class, "show"])
    ->name('articles.category');

Route::get('articles/{article}/relationships/author', [ArticleAuthorController::class, "index"])
    ->name('articles.relationships.author');

Route::patch('articles/{article}/relationships/author', [ArticleAuthorController::class, "update"])
    ->name('articles.relationships.author');

Route::get('articles/{article}/author', [ArticleAuthorController::class, "show"])
    ->name('articles.author');
    //    ->names('api.v1.categories')
//});

Route::withoutMiddleware(ValidateJsonApiDocument::class)
    ->post("login", LogginController::class)->name("login");
/*
Route::apiResource([
    'articles' => ArticleController::class,
    'categories' => CategoryController::class,
]); */
