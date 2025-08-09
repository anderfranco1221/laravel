<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_comments()
    {
        $this->postJson(route('api.v1.comments.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('comments', 0);
    }

    /** @test */
    public function can_create_comments()
    {

        $user = User::factory()->create();
        $article = Article::factory()->create();
        Sanctum::actingAs($user);
        $commentBody = 'Comment body';

        $response = $this->postJson(route('api.v1.comments.store'), [
            'body' => $commentBody,
            '_relationships' => [
                'article' => $article,
                'author' => $user,
            ],
        ])
            ->assertCreated();

        $comment = Comment::first();

        $response->assertJsonApiResource($comment, [
            'body' => $commentBody,
        ]);

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'body' => $commentBody,
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function body_is_requered()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.comments.store'), [
            'body' => null,
        ])->assertJsonApiValidationErrors('body');
    }

    /** @test */
    public function article_relationships_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'comment body',
        ])->assertJsonApiValidationErrors('data.relationships.article.data.id');
    }

    /** @test */
    public function article_must_exist_in_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'comment body',
            '_relationships' => [
                'article' => Article::factory()->make(),
            ],
        ])// ->dump()->assertJsonApiValidationErrors("relationships.article");//
            ->assertJsonApiValidationErrors('data.relationships.article.data.id');
    }

    /** @test */
    public function author_relationships_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'comment body',
            '_relationships' => [
                'article' => Article::factory()->create(),
            ],
        ])->assertJsonApiValidationErrors('data.relationships.author.data.id');
    }

    /** @test */
    public function author_must_exist_in_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.comments.store'), [
            'body' => 'comment body',
            '_relationships' => [
                'article' => Article::factory()->create(),
                'author' => User::factory()->make(['id' => 'uuid']),

            ],
        ])->assertJsonApiValidationErrors('data.relationships.author.data.id');
    }
}
