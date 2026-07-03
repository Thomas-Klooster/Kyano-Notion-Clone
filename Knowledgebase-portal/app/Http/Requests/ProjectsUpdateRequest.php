<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectsUpdateRequest extends FormRequest
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
            'name' => 'sometimes|required|string',
            'description' => 'sometimes|nullable|string',
            'slug' => ['sometimes', 'required', Rule::unique('projects', 'slug')->ignore($this->route('project'))],
            'customer_ids' => ['sometimes', 'array'],
            'customer_ids.*' => ['integer', 'exists:users,id'],
            'customerAccess' => ['sometimes', 'array'],
            'customerAccess.*' => ['integer', 'exists:users,id'],
            'category_id'  => 'sometimes|required|exists:categories,id',
        ];
    }
}
