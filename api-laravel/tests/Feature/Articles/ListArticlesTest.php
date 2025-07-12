<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_artique()
    {
        // $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.show', $article));

        $response->assertJsonApiResource($article, [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
        ])->assertJsonApiRelationshipLinks($article, ['category', 'author']);

        /* //dd($response);
        $response->assertExactJson([
            'data' =>[
                'type' => 'articles',
                'id'    => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]); */
    }

    /** @test */
    public function can_fetch_all_articles()
    {
        // $this->withoutExceptionHandling();

        $articles = Article::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.articles.index'));

        $response->assertJsonApiResourceCollection($articles, [
            'title', 'slug', 'content',
        ]);
    }

    /** @test */
    public function it_returns_a_json_api_error_object_when_an_article_is_not_found()
    {
        $this->getJson(route('api.v1.articles.show', 'not-exist'))
            ->assertJsonApiError(
                title: 'Not Found',
                detail: "No records found with the id 'not-exist' in the 'articles' resource.",
                status: '404');

        /* $response->assertJsonStructure([
            "errors" => [
                "*" => []
            ]
        ])->assertJsonFragment([
            "title" => "Not Found",
            "detail" => "No records found with the id 'not-exist' in the 'articles' resource.",
            "status" => "404"
        ])->assertStatus(404); */
    }
}
