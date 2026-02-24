<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class ProjectsController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return Project::with('category')->get();
    }
    // Create

   
        public function store(Request $request)
    {
        $data = $request->validate([
            'projectname' => 'required',
            'description' => 'required',
            'slug' => 'unique:projects,slug',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data['user_id'] = auth()->id();
        $project = Project::create($data);

        return response()->json($project, 201);
    }
    // Read

    
     public function show(Project $project)
     {
         $this->authorize('view', $project);
         return response()->json($project->load('category'));
    }

    // Update
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validate([
            'projectname' => 'sometimes|required',
            'description' => 'sometimes|required',
            'slug' => ['sometimes','required',
            Rule::unique('projects', 'slug')->ignore($project->id),
            ],
            'category_id'  => 'sometimes|required|exists:categories,id',
        ]);
        
        $project->update($data);
        return response()->json($project);
    }
    // Delete
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return response()->json(['deleted' => true]);
    }

  
  public function AdminIndex(Request $request) {
        // $this->authorize('admin');
        // $request->validate((['user_id' => 'required|exists:user,id'
        // ]));
        $query = Project::with('category');
        if ($request->user_id) {
        $query->where('user_id', $request->user_id);
         };

         return response()->json($query->get());
    }

     public function myProjects() {
        // 1. Projects with relationship category
        // 2. in column user_id authenticate the user id (token)
        // 3. Execute query as selected id
        return Project::with('category')->where('user_id', auth()->id())
        ->get();
    }
}