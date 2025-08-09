<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_the_associated_author_identifier()
    {
        // self::markTestSkipped();
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.relationships.author', $comment);

        $response = $this->getJson($url);

        $response->assertExactJson([
            'data' => [
                'type' => 'author',
                'id' => (string) $comment->author->getRouteKey(),
            ],
        ])->assertOk();
    }

    /** @test */
    public function can_fetch_the_associated_author_resource()
    {
        // self::markTestSkipped();
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.author', $comment);

        $this->getJson($url)->assertJson([
            'data' => [
                'type' => 'author',
                'id' => (string) $comment->author->getRouteKey(),
                'attributes' => [
                    'name' => $comment->author->name,
                ],
            ]]);
    }

    /** @test */
    public function can_update_the_associated_author()
    {
        $author = User::factory()->create();
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.author', $comment);

        $response = $this->patchJson($url, [
            'data' => [
                'type' => 'author',
                'id' => $author->id,
            ]]);

        $response->assertExactJson([
            'data' => [
                'type' => 'author',
                'id' => $author->id,
            ]]);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'user_id' => $author->id,
        ]);
    }

    /** @test */
    public function author_must_exist_in_database()
    {
        $comment = Comment::factory()->create();
        $url = route('api.v1.comments.relationships.author', $comment);

        $this->patchJson($url, [
            'data' => [
                'type' => 'author',
                'id' => 9999, // Assuming this ID does not exist
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }
}
