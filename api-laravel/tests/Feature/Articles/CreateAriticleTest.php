<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CreateAriticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guess_cannot_create_articles()
    {

        $this->postJson(route('api.v1.articles.store'))
            ->assertJsonApiError(
                title: "Unauthenticated",
                detail: "This action requires authentication.",
                status: "401"
            );

        $this->assertDatabaseCount("articles", 0);
    }


    /** @test */
    public function can_create_articles()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo',
            '_relationships' => [
                'category' => $category,
                "author" => $user
            ]
        ])->assertCreated();

        $article = Article::first();

        $response->assertJsonApiResource($article, [
            'title' => 'Nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del articulo'
        ]);

        $this->assertDatabaseHas("articles", [
            'title' => 'Nuevo articulo',
            "user_id" => $user->id,
            "category_id" => $category->id
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.articles.store'), [
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del articulo'
        ]);

        $response->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function slug_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Nuevo articulo',
                    'content' => 'Contenido del articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_be_unique()
    {
        Sanctum::actingAs(User::factory()->create());
        $article = Article::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Nuevo articulo',
                    'slug' => $article->slug,
                    'content' => 'Contenido del articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Nuevo articulo',
                    'slug' => '$#$$?)(&%$%#""',
                    'content' => 'Contenido del articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');

    }

     /** @test */
    public function slug_must_not_contain_underscores()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Nuevo articulo',
                    'slug' => 'holla_as',
                    'content' => 'Contenido del articulo'
        ])->assertSee(trans('validation.no_underscores', ['attribute' => 'data.attributes.slug']));

        $response->assertJsonApiValidationErrors('slug');

    }

     /** @test */
    public function slug_must_not_start_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Nuevo articulo',
                    'slug' => '-starts-with-dashe',
                    'content' => 'Contenido del articulo'
        ])->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']));

        $response->assertJsonApiValidationErrors('slug');

    }

     /** @test */
    public function slug_must_not_finish_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Nuevo articulo',
                    'slug' => 'finish-with-dashe-',
                    'content' => 'Contenido del articulo'
        ])->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']));

        $response->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function content_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.articles.store'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo articulo',
                    'slug' => 'nuevo-articulo',
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('content');

    }
}
