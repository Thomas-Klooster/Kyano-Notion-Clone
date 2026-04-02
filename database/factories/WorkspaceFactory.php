<?php
namespace Database\Factories;

use App\Models\Workspace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkspaceFactory extends Factory
{

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace>
 */

    protected $model = Workspace::class;
        /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'owner_id' => User::factory(),
            'slug' => Str::slug($name),
        ];
    }

public function configure(): static
{
    return $this->afterCreating(function (Workspace $workspace) {
        $users = User::inRandomOrder()->take(rand(2, 5))->pluck('id');

        $workspace->members()->attach($users, [
            'role' => 'member',
        ]);
    });
}}