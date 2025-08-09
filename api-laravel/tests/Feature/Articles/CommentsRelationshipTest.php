<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentsRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_comments_identifiers()
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $response = $this->getJson($url);
        $response->assertJsonCount(2, 'data');

        $article->comments->each(function ($comment, $key) use ($response) {
            $response->assertJsonFragment([
                'type' => 'comments',
                'id' => (string) $comment->getRouteKey(),
            ]);
        });
    }

    /** @test */
    public function it_returns_an_empty_array_when_there_are_no_associated_comments()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.relationships.comments', $article);

        $response = $this->getJson($url);
        $response->assertJsonCount(0, 'data');

        $response->assertExactJson([
            'data' => [],
        ]);
    }

    /** @test */
    public function can_fetch_the_associated_comments_resourse()
    {
        $article = Article::factory()->hasComments(2)->create();

        $url = route('api.v1.articles.comments', $article);

        $response = $this->getJson($url);
        $response->assertJson([
            'data' => $article->comments->map(function ($comment) {
                return [
                    'type' => 'comments',
                    'id' => (string) $comment->getRouteKey(),
                    'attributes' => [
                        'body' => $comment->body,
                    ],
                ];
            })->toArray(),
        ]);
    }
}
