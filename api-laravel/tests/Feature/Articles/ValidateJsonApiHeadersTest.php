<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function accept_header_must_be_present_in_all_requests()
    {
        Route::get('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeaders::class);

        $this->get('test_route')->assertStatus(406);

        $this->get('test_route', [
            'accept' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_must_be_present_on_all_post_requests()
    {
        Route::post('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeaders::class);

        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertSuccessful();

        // dd("! por que esa aqui");
    }

    /** @test */
    public function content_type_must_be_present_on_all_patch_requests()
    {
        Route::patch('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeaders::class);

        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_reponses()
    {
        Route::get('test_route', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeaders::class);

        $this->get('test_route', [
            'accept' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');
    }
}
