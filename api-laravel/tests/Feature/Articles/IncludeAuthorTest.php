<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeAuthorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_related_author_of_on_article()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'author',
        ]);

        $this->getJson($url)->assertJson([

            'included' => [
                [
                    'type' => 'author',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name,
                    ],
                ],
            ],
        ]);

    }

    /** @test */
    public function can_include_related_authors_of_multiple_articles()
    {
        $article = Article::factory()->create()->load('author');
        $article2 = Article::factory()->create()->load('author');

        $url = route('api.v1.articles.index', [
            'include' => 'author',
        ]);

        /* \DB::listen(function($query){
            dump($query->sql);
        }); */

        $this->getJson($url)->assertJson([

            'included' => [
                [
                    'type' => 'author',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name,
                    ],
                ],
                [
                    'type' => 'author',
                    'id' => $article2->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article2->author->name,
                    ],

                ],
            ],
        ]);

    }
}
