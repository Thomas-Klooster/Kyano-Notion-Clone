<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Article;

class ArticleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required',
            'content' => 'sometimes|required',
            'summary' => 'sometimes|nullable',
            'visibility' => 'sometimes|required',
            'status' => 'sometimes|required',
            'slug' => 'sometimes|required',
            'article_cover' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || preg_match('/^#[0-9A-F]{6}$/i', $value)) {
                        return;
                    }

                    if (preg_match('/^attachment:(\d+)$/', $value, $matches)) {
                        $article = $this->route('article');

                        if ($article instanceof Article && $article->attachments()->whereKey((int) $matches[1])->exists()) {
                            return;
                        }
                    }

                    $fail('De gekozen artikel cover is ongeldig.');
                },
            ],
            'project_id' => 'sometimes|required|exists:projects,id',
            'category_id' => 'sometimes|required|exists:categories,id'
        ];

    }
}
