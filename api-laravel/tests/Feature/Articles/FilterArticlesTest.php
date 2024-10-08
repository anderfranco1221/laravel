<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_articles_by_title()
    {
        Article::factory()->create([
            'title' => 'Aprendible laravel'
        ]);

        Article::factory()->create([
            'title' => 'Other Aprendible'
        ]);

        $url = route('api.v1.articles.index', [
            'filter' =>[
                'title' => 'laravel'
            ]
        ]);
        
        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprendible laravel')
            ->assertDontSee('Other Aprendible');
    }

    /** @test */
    public function can_filter_articles_by_content()
    {
        Article::factory()->create([
            'content' => 'Aprendible laravel'
        ]);

        Article::factory()->create([
            'content' => 'Other Aprendible'
        ]);

        $url = route('api.v1.articles.index', [
            'filter' =>[
                'content' => 'laravel'
            ]
        ]);
        
        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprendible laravel')
            ->assertDontSee('Other Aprendible');
    }

    /** @test */
    public function can_filter_articles_by_year()
    {
        Article::factory()->create([
            'title' => 'Aprendible laravel 2021',
            'created_at' => now()->year(2021)
        ]);

        Article::factory()->create([
            'title' => 'Other Aprendible 2022',
            'created_at' => now()->year(2022)
        ]);

        $url = route('api.v1.articles.index', [
            'filter' =>[
                'year' => '2021'
            ]
        ]);
        
        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprendible laravel 2021')
            ->assertDontSee('Other Aprendible 2022');
    }

    /** @test */
    public function can_filter_articles_by_month()
    {
        Article::factory()->create([
            'title' => 'Aprendible laravel 3',
            'created_at' => now()->month(3)
        ]);

        Article::factory()->create([
            'title' => 'Other Aprendible 5',
            'created_at' => now()->month(5)
        ]);

        Article::factory()->create([
            'title' => 'Aprendible laravel 3 * 3',
            'created_at' => now()->month(3)
        ]);

        $url = route('api.v1.articles.index', [
            'filter' =>[
                'month' => '3'
            ]
        ]);
        
        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Aprendible laravel 3')
            ->assertSee('Aprendible laravel 3 * 3')
            ->assertDontSee('Other Aprendible 5');
    }

    /** @test */
    public function cannot_filter_articles_by_unkown_filters()
    {
        Article::factory()->count(2)->create();

        $url = route('api.v1.articles.index', [
            'filter' =>[
                'unkown' => 'unkown'
            ]
        ]);
        
        $this->getJson($url)->assertStatus(400);
    }
}
