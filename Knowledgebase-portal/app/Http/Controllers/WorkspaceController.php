<?php

namespace App\Http\Controllers;

use App\Http\Requests\addMemberRequest;
use App\Http\Resources\WorkspaceResource;
use App\Mail\InviteMail;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Http\Requests\WorkspaceRequest;
use App\Http\Requests\WorkspaceUpdateRequest;
use App\Http\Resources\UserResource;
class WorkspaceController extends Controller
{
    use AuthorizesRequests;

public function index()
{
    $workspaces = Workspace::visibleTo(auth('sanctum')->user())
        ->with([
            'categories.projects.articles.tags',
            'members:id,name,email,company,address,phone_number,role',
        ])
        ->latest()
        ->get();

    return WorkspaceResource::collection($workspaces);
}
    public function store(WorkspaceRequest $request) {
        $this->authorize('create', Workspace::class);
        $request->validated();

        $workspace = Workspace::create([
            'name' => $request->name,
            'slug' => '',
            'owner_id' => auth()->id(),
        ]);
 
        $workspace->members()->syncWithoutDetaching([
            auth()->id() => ['role' => 'owner'],
        ]);
        return response()->json($workspace, 201);
    }
    public function show(Workspace $workspace) {
        $this->authorize('view', $workspace);
        return new WorkspaceResource(
            $workspace->load([
                'categories.projects.articles.tags',
                'members:id,name,email,company,address,phone_number,role',
            ])
        );
    }
    
    public function update(Workspace $workspace, WorkspaceUpdateRequest $request) {
        $this->authorize('update', $workspace);
        $data = $request->validated();
        $workspace->update(['name' => $data['name']]);

    if (isset($data['customer_ids'])) {
        $currentOwner = $workspace->owner_id;

        $syncData = collect($data['customer_ids'])
         ->mapWithKeys(fn($id) => [$id => ['role' => 'member']])
         ->toArray();
        $syncData[$currentOwner] = ['role' => 'owner'];
        $workspace->members()->sync($syncData);
    }
    return response()->json($workspace->load('members'));
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

    public function invite(Request $request, Workspace $workspace)
    {
    $this->authorize('invite', $workspace);

    $data = $request->validate([
        'email' => ['required', 'email'],
        'role' => ['nullable', 'string', 'in:admin,member'],
    ]);

    $user = User::where('email', $data['email'])->first();
    if (!$user) {
        return response()->json(['message' => 'Geen gebruiker gevonden met dit e-mailadres.'], 404);
    }

    if ($workspace->members()->where('user_id', $user->id)->exists()) {
        return response()->json(['message' => 'Gebruiker is al lid van deze workspace.'], 409);
    }

    $acceptUrl = URL::temporarySignedRoute(
        'workspace.invite.accept',
        now()->addDays(7),
        [
            'workspace' => $workspace->getRouteKey(),
            'user' => $user->id,
            'role' => $data['role'] ?? 'member',
        ],
    );

    Mail::to($user->email)->send(new InviteMail($workspace, $acceptUrl));

    return response()->json(['message' => 'Uitnodiging verzonden.']);
    }

    public function acceptInvite(Request $request)
    {
    if (!$request->hasValidSignature()) {
        return response()->json(['message' => 'Deze uitnodigingslink is ongeldig of verlopen.'], 403);
    }

    $user = $request->user();
    $invitedUserId = (int) $request->query('user');

    if (!$user || $user->id !== $invitedUserId) {
        return response()->json(['message' => 'Log in met het uitgenodigde account om deze workspace te accepteren.'], 403);
    }

    $workspace = Workspace::where('slug', $request->query('workspace'))->firstOrFail();
    $role = $request->query('role', 'member');

    if (!in_array($role, ['admin', 'member'], true)) {
        return response()->json(['message' => 'Ongeldige uitnodigingsrol.'], 422);
    }

    $workspace->members()->syncWithoutDetaching([
        $user->id => ['role' => $role],
    ]);

    return response()->json(['message' => 'Uitnodiging geaccepteerd.']);
    }


    public function availableUsers(Workspace $workspace) {
    $this->authorize('manage', $workspace);

    $existingMemberIds = $workspace->members()->pluck('users.id');

    $users = User::whereNotIn('id', $existingMemberIds)
        ->select(['id', 'name', 'email', 'company', 'address', 'phone_number', 'role'])
        ->orderBy('name')
        ->paginate(20);

    return UserResource::collection($users);
}


        public function removeMember(Workspace $workspace, User $user) {
        $this->authorize('manage', $workspace);

        if ((int) $workspace->owner_id === (int) $user->id) {
            return response()->json([
                'message' => 'De eigenaar kan niet uit de workspace worden verwijderd zonder eerst eigenaarschap over te dragen.',
            ], 422);
        }

        $workspace->members()->detach($user->id);
        return response()->json(['message' => 'Gebruiker is verwijderd'], 200);
    }

    public function destroy(Workspace $workspace) {
        $this->authorize('delete', $workspace);
        $workspace->delete();
        return response()->json(['message' => 'Succesvol verwijderd.']);
    }
}
