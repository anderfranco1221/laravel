<?php

namespace Tests\Feature\Authors;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListAuthorsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_a_single_author()
    {
        $author  = User::factory()->create();

        $response = $this->getJson(route('api.v1.author.show', $author));

        $response->assertJsonApiResource($author, [
            'name' => $author->name
        ]);

        $this->assertTrue(
            Str::isUuid($response->json("data.id")),
            "The author 'id' must be Uuid"
        );
    }

    /** @test */
    public function can_fetch_all_authors()
    {
        $author = User::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.author.index'));

        $response->assertJsonApiResourceCollection($author,
                ['name']
        );
    }
}
