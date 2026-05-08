<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $files = [
            ['mime' => 'application/pdf', 'name' => 'handleiding.pdf'],
            ['mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'name' => 'specificatie.docx'],
            ['mime' => 'image/png', 'name' => 'screenshot.png'],
            ['mime' => 'image/jpeg', 'name' => 'mockup.jpg'],
        ];

        $file = fake()->randomElement($files);

        return [
            'article_id' => Article::factory(),
            'path' => 'attachments/' . fake()->uuid() . '-' . $file['name'],
            'mime' => $file['mime'],
            'original_name' => $file['name'],
            'size' => fake()->numberBetween(50_000, 5_000_000),
        ];
    }
}
