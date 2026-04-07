<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;
use Illuminate\Support\Str;

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
            'project_id' => Project::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'workspace_id' => Workspace::inRandomOrder()->first()->id,
            'status' => $this->faker->randomElement(['draft', 'published']),
            'visibility' => $this->faker->randomElement(['public', 'private']),
        ];
    }
}
