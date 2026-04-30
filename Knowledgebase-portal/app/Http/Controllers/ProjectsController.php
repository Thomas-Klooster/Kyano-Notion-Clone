<?php
namespace App\Http\Controllers;


use App\Http\Resources\ProjectResource;
use App\Http\Requests\ProjectsUpdateRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\ProjectsRequest;
class ProjectsController extends Controller
{
    use AuthorizesRequests;

     public function index() {
       $projects = Project::with(['category', 'article', 'workspace'])
       ->visibleTo(auth('sanctum')->user())->get();
        return ProjectResource::collection($projects);        
    }
    public function store(ProjectsRequest $request, Project $project)
    {
        $this->authorize('create', Project::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $project = Project::create($data);
        return (new ProjectResource($project))->response()->setStatusCode(201);
    }
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        return new ProjectResource($project->load(['category', 'article', 'workspace']));
    }
    public function update(ProjectsUpdateRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        $data = $request->validated();
        $project->update($data);    
        return ProjectResource::collection($project);
        }
    
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        
        return response()->json(['deleted' => true]);
    }
    public function AdminIndex(Request $request) {
        $query = Project::with(['category', 'article', 'workspace']);
        if ($request->user_id) {
        $query->where('user_id', $request->user_id);
         };
        
         return ProjectResource::collection($query->get());
    }
     
    
    public function myProjects()
{
    $projects = Project::with(['category', 'article', 'workspace'])
    ->visibleTo(auth('sanctum')->user())->get();
    return ProjectResource::collection($projects);
}

}