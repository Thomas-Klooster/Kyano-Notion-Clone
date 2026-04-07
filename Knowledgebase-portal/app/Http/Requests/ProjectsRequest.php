<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $projectId = $this->input('project_id');

        if (!$projectId) return
        in_array(auth()->user()->role, ['admin', 'owner']);

        $project = \App\Models\Category::find($projectId);
        if (!$project) return false;
        
        return auth()->user()->role === 'admin' ||
        $project->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'projectname' => 'required|string',
            'description' => 'required|string',
            'slug' => 'unique:projects,slug',
            'category_id' => 'required|exists:categories,id',
            // 'article_id' => 'nullable|exists:articles,id',
            'workspace_id' => 'required|exists:workspaces,id'
        ];
    }


    public function messages(): array {
        return [
            'projectname.required' => 'Het invullen van jouw projectnaam is verplicht.',
            // 'description.required' => 'Het invullen van een descriptie is verplicht.',
            'category_id.required' => 'Een categorie is verplicht om een project aan te maken.',
            'workspace_id.required' => 'Een workspace is verplicht om een project aan te maken.'            
            ];
    }
}
