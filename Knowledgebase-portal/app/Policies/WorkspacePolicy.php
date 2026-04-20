<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Create a new policy instance.
     */
     
    public function before(User $user): ?bool {
    if ($user->role === 'admin') return true;
    return null;
}
public function viewAny(User $user): bool {
    return $user->workspaces()->exists();
}

public function create(User $user): bool {
    return false;
}

public function view(User $user, Workspace $workspace): bool {
    return $this->hasWorkspaceRole($user, $workspace, ['owner', 'admin', 'member']);
}

public function update(User $user, Workspace $workspace): bool {
    return $this->hasWorkspaceRole($user, $workspace, ['owner', 'admin']);
}

public function delete(User $user, Workspace $workspace): bool {
    return $this->hasWorkspaceRole($user, $workspace, ['owner']);
}

public function manage(User $user, Workspace $workspace): bool {
    return $this->hasWorkspaceRole($user, $workspace, ['owner', 'admin']);
}
    public function invite(User $user, Workspace $workspace): bool {
    return $this->manage($user, $workspace);
}

public function addMember(User $authUser, Workspace $workspace): bool
{
    return $workspace->members()
        ->where('user_id', $authUser->id)
        ->whereIn('workspace_role', ['owner', 'admin'])
        ->exists();
}
    private function hasWorkspaceRole(User $user, Workspace $workspace, array $roles): bool {
    return $workspace->members()
        ->where('user_id', $user->id)
        ->wherePivotIn('role', $roles)
        ->exists();
}
}
