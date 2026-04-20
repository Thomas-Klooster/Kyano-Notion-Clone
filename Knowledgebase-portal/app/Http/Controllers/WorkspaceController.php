<?php

namespace App\Http\Controllers;

use App\Http\Requests\addMemberRequest;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use App\Http\Requests\WorkspaceRequest;
use App\Http\Requests\WorkspaceUpdateRequest;
use App\Http\Resources\UserResource;
class WorkspaceController extends Controller
{
    use AuthorizesRequests;

    public function index() {
        $this->authorize('viewAny', Workspace::class);
        $workspaces = Workspace::visibleTo(auth()->user())
        ->with('categories.projects.articles')->latest()->get();
        return response()->json($workspaces);
    }
    
    public function store(WorkspaceRequest $request) {
        $this->authorize('create', Workspace::class);
        $request->validated();

        $workspace = Workspace::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'owner_id' => auth()->id(),
        ]);
 
        $workspace->members()->attach(auth()->id(), ['role' => 'owner']);
        return response()->json($workspace, 201);
    }
        public function show(Workspace $workspace) {
        $this->authorize('view', $workspace);
        return 
        $workspace->load(['articles', 'projects']);
    }

    public function update(Workspace $workspace, WorkspaceUpdateRequest $request) {
        $this->authorize('update', $workspace);
        $data = $request->validated();

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . uniqid();
        }

        $workspace->update($data);
        return response()->json($workspace);
    }

    public function workspaceArticles(Workspace $workspace) {
        $this->authorize('view', $workspace);
        return $workspace->articles()->get();
    } 

    public function workspaceProjects(Workspace $workspace)
    {
        $this->authorize('view', $workspace);
        return $workspace->projects()->get();
    }
    
    public function addMember(addMemberRequest $request, Workspace $workspace) {
    $this->authorize('manage', $workspace);
    $request->validated();

    $user = User::findOrFail($request->user_id);

    if ($workspace->members()->where('user_id', $user->id)->exists()) {
        return response()->json(['message' => 'Gebruiker is al lid van deze workspace'], 409);
    }

    $workspace->members()->attach($user->id, [
        'role' => $request->role ?? 'member',
    ]);

    return response()->json(['message' => 'Gebruiker is toegevoegd aan de workspace.'], 201);

    }        


    public function availableUsers(Workspace $workspace) {
    $this->authorize('manage', $workspace);

    $existingMemberIds = $workspace->members()->pluck('users.id');

    $users = User::whereNotIn('id', $existingMemberIds)
        ->select(['id', 'name', 'email', 'address', 'phone', 'role'])
        ->orderBy('name')
        ->paginate(20);

    return UserResource::collection($users);
}


        public function removeMember(Workspace $workspace, User $user) {
        $this->authorize('manage', $workspace);
        $workspace->members()->detach($user->id);
        return response()->json(['message' => 'Gebruiker is verwijderd'], 200);
    }

    public function destroy(Workspace $workspace) {
        $this->authorize('delete', $workspace);
        $workspace->delete();
        return response()->json(['message' => 'Succesvol verwijderd.']);
    }
}