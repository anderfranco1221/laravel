<?php

namespace Tests\Feature\Articles;

use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;


     /** @test */
    public function gessts_cannot_update_articles()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article))
            ->assertJsonApiError(
                title: "Unauthenticated",
                detail: "This action requires authentication.",
                status: "401"
            );
    }

     /** @test */
    public function can_update_owned_articles()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author, ["article:update"]);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Update articulo',
            'slug' => $article->slug,
            'content' => 'Actualizar contenido del articulo'
        ])->assertOk();

        $article = Article::first();

        $response->assertJsonApiResource($article, [
            'title' => 'Update articulo',
            'slug' => $article->slug,
            'content' => 'Actualizar contenido del articulo'
        ]);
    }

    /** @test */
    public function cannot_update_owned_by_other_users()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Update articulo',
            'slug' => $article->slug,
            'content' => 'Actualizar contenido del articulo'
        ])->assertForbidden();
    }

    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->create();
        Sanctum::actingAs($article->author);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
                    'slug' => 'update-articulo',
                    'content' => 'Actualizar Contenido del articulo'
        ]);

        $response->assertJsonApiValidationErrors('title');

    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs($article->author);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Update articulo',
                    'content' => 'Actualizar Contenido del articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article1 = Article::factory()->create();

        Sanctum::actingAs($article1->author);

        $article2 = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article1), [
                    'title' => 'Nuevo articulo',
                    'slug' => $article2->slug,
                    'content' => 'Contenido del articulo'
        ]);

        $response->assertJsonApiValidationErrors('slug');

    }

    /** @test */
    public function content_is_required()
    {
        //$this->withoutExceptionHandling();
        $article = Article::factory()->create();
        Sanctum::actingAs($article->author);

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Update articulo',
                    'slug' => 'update-articulo',
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('content');

    }
}
