<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Create a new policy instance.
     */
    // public function __construct()
    // {
        
    // }

    public function viewAny(User $user, Workspace $workspace) {
        if ($user->role === 'admin') return true;
        return $workspace->owner_id === $user->id;
    }
   public function create(User $user ) {
        if ($user->role === 'admin') return true;
        return false;
    }
    public function view(User $user, Workspace $workspace) {
        if ($user->role === 'admin') return true;
        return $this->hasWorkspaceRole($user, $workspace, ['owner', 'admin']);
    }

    
    public function update(User $user, Workspace $workspace): bool {
    if ($user->role === 'admin') return true;
    return $this->hasWorkspaceRole($user, $workspace, ['owner', 'admin']);
}

 
    public function delete(User $user, Workspace $workspace): bool {
    if ($user->role === 'admin') return true;
    return $this->hasWorkspaceRole($user, $workspace, ['owner']);
}

    public function manage(User $user, Workspace $workspace): bool {
    if ($user->role === 'admin') return true;
    return $this->hasWorkspaceRole($user, $workspace, ['owner', 'admin']);
}

    public function invite(User $user, Workspace $workspace): bool {
    return $this->manage($user, $workspace);
}
    private function hasWorkspaceRole(User $user, Workspace $workspace, array $roles): bool {
    return $workspace->members()
        ->where('user_id', $user->id)
        ->wherePivotIn('role', $roles)
        ->exists();
}
}
