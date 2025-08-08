<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\Api\LogginController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Middleware\ValidateJsonApiHeaders;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Controllers\ArticleAuthorController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\Api\CommentArticleController;

/*
Route::bind('article', function($article){
    return \App\Models\Article::where('slug', $article)
    ->sparseFieldset()
    ->firstOrFail();
});*/

// Route::name('api.v1.', function(){

Route::apiResource('articles', ArticleController::class);
Route::apiResource('comments', CommentController::class);
Route::apiResource('categories', CategoryController::class)
    ->only('index', 'show');
Route::apiResource('author', AuthorController::class)
    ->only('index', 'show');

Route::controller(CommentArticleController::class)
    ->prefix('comments/{comment}')
    ->group(function () {
        Route::get('/relationships/article', 'index')
            ->name('comments.relationships.article');
        Route::get('/article', 'show')
            ->name('comments.article');
        Route::patch('/relationships/article', 'update');
            /* ->name('comments.relationships.article') */
    });


Route::prefix('articles/{article}')
    ->group(function () {
        Route::controller(ArticleCategoryController::class)
            //->prefix('articles/{article}')
            ->group(function () {
                Route::get('relationships/category', 'index')
                    ->name('articles.relationships.category');
                Route::get('category', 'show')
                    ->name('articles.category');
                Route::patch('relationships/category', 'update');
                    /* ->name('articles.relationships.category') */
        });


        Route::controller(ArticleAuthorController::class)
                //->prefix('articles/{article}')
                ->group(function () {
                    Route::get('relationships/author', 'index')
                        ->name('articles.relationships.author');
                    Route::get('author', 'show')
                        ->name('articles.author');
                    Route::patch('relationships/author', 'update');
                        /* ->name('articles.relationships.author') */
        });
});
Route::withoutMiddleware([ValidateJsonApiDocument::class, ValidateJsonApiHeaders::class])
    ->group(function () {
        Route::post('login', LogginController::class)->name('login');
        Route::post('logout', LogoutController::class)->name('logout');
        Route::post('register', RegisterController::class)->name('register');

    });


