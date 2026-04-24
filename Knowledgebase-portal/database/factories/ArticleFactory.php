<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Workspace;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->name(),
            'content' => $this->faker->paragraph(2, true),
            'summary' => $this->faker->paragraphs(1, true),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
            'workspace_id' => Workspace::inRandomOrder()->first()->id ?? Workspace::factory(),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'visibility' => $this->faker->randomElement(['public', 'private']),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Article $article) {
            $tagIds = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $article->tags()->syncWithoutDetaching($tagIds);
                    });
    }
}