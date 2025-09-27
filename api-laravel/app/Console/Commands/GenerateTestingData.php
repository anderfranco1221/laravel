<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class GenerateTestingData extends Command
{

    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for the API.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if(! $this->confirmToProceed())
            return;

        User::query()->delete();
        Article::query()->delete();
        Category::query()->delete();
        Comment::query()->delete();

        $user = User::factory()->hasArticles(1)->create([
            "name" => "Anderson Franco",
            'email' => "anderfranco1221@gmail.com"
        ]);

        $articles = Article::factory(14)->hasComments(5)->create();

        $this->info('User UUID');
        $this->line($user->id);

        $this->info("Token: ");
        $this->line($user->createToken("Anderson")->plainTextToken);

        $this->info("Article ID:");
        $this->line($user->articles->first()->id);

        $this->info("Category ID:");
        $this->line($articles->first()->category->id);

        $this->info("Comment ID:");
        $this->line($articles->first()->comments->random()->id);

    }
}
