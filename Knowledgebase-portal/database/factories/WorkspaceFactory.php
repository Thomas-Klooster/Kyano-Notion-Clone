<?php
namespace Database\Factories;

use App\Models\Workspace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkspaceFactory extends Factory
{

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
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
        $admin = User::where('email', 'Admin@gmail.com')->first();

        return [
            'name' => $name,
            'owner_id' => $admin->id,
            'slug' => Str::slug($name),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Workspace $workspace) {
            $workspace->members()->attach($workspace->owner_id, [
                'role' => 'owner',
            ]);
        });
    }
}