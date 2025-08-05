<?php

namespace Tests\Feature\Comments;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_update_comments()
    {
        $comment = Comment::factory()->create();

        $this->patchJson(route("api.v1.comments.update", $comment))
        ->assertJsonApiError(
            title: "Unauthenticated",
            detail: "This action requires authentication.",
            status: "401"
        );
    }

    /** @test */
    public function can_update_owned_comments(){
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ["comment:update"]); // ,

        $response = $this->patchJson(route("api.v1.comments.update", $comment),[
            "body" => "Update content"
        ])->assertOk();

        $response->assertJsonApiResource($comment, [
            "body" => "Update content"
        ]);
    }


    /** @test */
    public function can_update_owned_comments_with_relationships(){
        $comment = Comment::factory()->create();
        $article = Article::factory()->create();

        Sanctum::actingAs($comment->author, ["comment:update"]);

        $response = $this->patchJson(route("api.v1.comments.update", $comment),[
            "body" => "Update content",
            "_relationships" => [
                "article" => $article,
                "author" => $comment->author
            ]
        ])->assertOk();

        $response->assertJsonApiResource($comment, [
            "body" => "Update content"
        ]);

        $this->assertTrue($article->is($comment->fresh()->article));

        $this->assertDatabaseHas("comments", [
            "body" => "Update content",
            "article_id" => $article->id,
            "user_id" => $comment->author->id,
        ]);
    }

    /** @test */
    public function can_update_owned_comments_owned_by_other_users(){
        $comment = Comment::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ["comment:update"]);

        $this->patchJson(route("api.v1.comments.update", $comment),[
            "body" => "Update content",
            "_relationships" => [
                "article" => $comment->article,
                "author" => $comment->author
            ]
        ])->assertForbidden();
    }

    /** @test */
    public function body_is_required(){
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author); // , ["comment:update"]

        $response = $this->patchJson(route("api.v1.comments.update", $comment),[
            "body" => null
        ])->assertJsonApiValidationErrors("body");
    }
}
