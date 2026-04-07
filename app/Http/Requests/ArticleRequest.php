<?php

namespace App\Http\Requests;

use App\Models\Workspace;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'title' => 'required|string',
            'content' => 'required|string',
            'summary' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'category_id' => 'required|exists:categories,id',
            'workspace_id' => 'required|exists:workspaces,id',
            'visibility' => 'required|in:public,private',
            'status' => 'required|in:draft,published,',
            'article_id' => 'nullable|exists:articles,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,webm|max:10240',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
{
    
      $articleId = $this->input('article_id');

      if (!$articleId)
        return in_array(auth()->user()->role, ['admin', 'owner']);

      $article = \App\Models\Article::find($articleId);
      if (!$article) return false;

      return in_array(auth()->user()->role, ['admin', 'owner'])
         || $article->user_id === auth()->id();
}
     
    protected function prepareForValidation(): void
{
    if ($this->hasFile('attachments')) {
        $this->merge([
            'attachments' => $this->file('attachments'),
        ]);
    }
}

    public function messages() {

    return [
        'title.required' => 'Het invullen van een titel is verplicht.',
        'content.required' => 'Het invullen van een titel is verplicht.',
        'project_id.nullable' => 'Voeg een project toe! (niet verplicht)',
        'category_id.required' => 'Voeg een categorie erbij.', 
        'workspace_id.required' => 'Een workspace is verplicht om een project aan te maken.',
        'attachments.*.file' => 'Voeg een geldig bestand in.',
        'attachments.*.mimes' => 'Alleen jpg, png, pdf, doc en docx zijn toegestaan.',
        'attachments.*.max' => 'Een bestand mag niet groter zijn dan 10MB.',
    ];
    }
}


