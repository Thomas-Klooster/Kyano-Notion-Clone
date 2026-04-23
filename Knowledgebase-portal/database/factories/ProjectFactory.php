<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Workspace;
use App\Models\Article;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */

class ProjectFactory extends Factory
{
    
        protected $model = Project::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    
public function definition(): array
{

    $name = $this->faker->company();
    return [
        'name' => $name,
        'description' => $this->faker->paragraph(4, true),
        'slug' => Str::slug($name),
        'article_id' => Article::inRandomOrder()->first()->id ?? Article::factory(),
        'workspace_id' => Workspace::inRandomOrder()->first()->id ?? Workspace::factory(),
        'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
        'user_id' => User::factory(),
    ];
}
}