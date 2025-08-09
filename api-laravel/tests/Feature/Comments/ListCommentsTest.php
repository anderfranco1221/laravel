<?php

namespace Tests\Feature\Comments;

use Tests\TestCase;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCommentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->getJson(route('api.v1.comments.show', $comment));

        $response->assertJsonApiResource($comment, [
            'body' => $comment->body,
        ]);
        // ->assertJsonApiRelationshipLinks($comment);
    }

    /** @test */
    public function can_fetch_all_comments()
    {
        // $this->withoutExceptionHandling();

        $comments = Comment::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.comments.index'));

        $response->assertJsonApiResourceCollection($comments, ['body']);
    }

    /** @test */
    public function it_returns_a_json_api_error_object_when_an_comment_is_not_found()
    {
        $this->getJson(route('api.v1.comments.show', 'not-exist'))
            ->assertJsonApiError(
                title: 'Not Found',
                detail: "No records found with the id 'not-exist' in the 'comments' resource.",
                status: '404');

        /* $response->assertJsonStructure([
            "errors" => [
                "*" => []
            ]
        ])->assertJsonFragment([
            "title" => "Not Found",
            "detail" => "No records found with the id 'not-exist' in the 'comments' resource.",
            "status" => "404"
        ])->assertStatus(404); */
    }
}
