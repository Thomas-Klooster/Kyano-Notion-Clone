<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{

public function index() {
    return 
    Project::with('category')->get();
}
    public function store(Request $request) {
        $data = $request->validate([
            'projectname' => 'required',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id'
        ]);
        $project = Project::create($data);

        return response()->json($project, 201);
    }

    public function show (Project $project) {
    return
    $project->load('category');
    }

    public function update(Request $request, Project $project) {
        $data =$request->validate([
            'projectname' => 'sometimes|required',
            'description' => 'sometimes|required',
            'category_id' => 'sometimes|required|exists|categories,id'
        ]);
            $project->update($data);
            return $project;
        }

        public function destroy(Project $project) {
            $project->delete();
            return response()->json([
                'message' => 'deleted'
            ]);
        }

}

// store request
// project = project create make a request validate and add projectname and description as required