<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
{
    $categoryId = $this->input('category_id');

    if(!$categoryId) return 
        in_array(auth()->user()->role, ['admin', 'owner']);

    $category = \App\Models\Category::find($categoryId);
    if (!$category) return false;

    return auth()->user()->role === 'admin' ||
    $category->user_id === auth()->id();
}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'slug' => 'unique:categories,slug'
        ];
    }


    public function messages() {
        return [
        
        'name.required' => 'Het invullen van een naam is verplicht.'
    ];
    }
}
