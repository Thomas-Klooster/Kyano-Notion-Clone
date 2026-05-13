<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
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
        'feedback' => 'nullable|string',
        'helpful' => 'required|boolean',
        'article_id' => 'nullable|exists:article,id',
        ];
    }


    // Forces to accept the helpful boolean
protected function prepareForValidation()
{
    if ($this->has('helpful') && !is_null($this->helpful)) {
        $this->merge([
            'helpful' => filter_var($this->helpful, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
    public function messages() {
        return [
        'helpful.required' => 'Beoordeel ons!',
        ];
    }
}
