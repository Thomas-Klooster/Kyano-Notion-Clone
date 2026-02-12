<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class ArticleRequest extends FormRequest
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
            'title'=> 'required|string',
            'content'=> 'required|string',
            'summary'=> 'string',
            'category_id' => 'string',
        ];
    }


    public function messages(): array
    {
        return [
    'title'=> 'Vul een titelnaam in.',
    'content'=> 'Descriptie in hier.',
    'summary'=> 'Kort descriptie',
    'category_id'=> 'Wat voor categorie?'
        ];
}
    }
