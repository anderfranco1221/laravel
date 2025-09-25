<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExceptionsHandlerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function json_api_errors_are_only_shown_to_requests_with_the_prefix_api()
    {
        $this->getJson("api/route")
            ->assertJsonApiError(
                detail: "The route api/route could not be found."
            );

        $this->getJson("api/v1/invalid-route")
            ->assertJsonApiError(
                detail: "The route api/v1/invalid-route could not be found."
            );
    }

    /** @test */
    public function default_laravel_error_is_only_shown_to_requests_without_the_prefix_api()
    {
        $this->getJson("non/api/route")
            ->assertJson([
                "message" => "The route non/api/route could not be found.",
            ]);

        $this->withoutJsonApiHelpers()->getJson("non/api/route")
            ->assertJson([
                "message" => "The route non/api/route could not be found.",
            ]);
    }
}
