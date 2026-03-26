<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkspaceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
    $workspaceId = $this->input('workspace_id');
    if(!$workspaceId) return 
    in_array(auth()->user()->role, ['admin', 'owner']);

    $workspace = \App\Models\Workspace::find($workspaceId);
    if (!$workspace) return false;

    return auth()->user()->role === 'admin' ||
    $workspace->user_id === auth()->id();

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
        ];
    }


    public function messages() {
        return [
        'name.required' => 'Je bent verplicht om jouw workspace een naam te geven.'
        ];
    }
}
