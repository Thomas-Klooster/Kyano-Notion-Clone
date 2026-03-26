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
        // return in_array($user->role, ['admin', 'owner']);
    }
   public function create(User $user ) {
        return 
        in_array($user->role, ['admin', 'owner']);
    }
    public function view(User $user, Workspace $workspace) {
        if ($user->role === 'admin') return true;
        return $workspace->owner_id === $user->id;
    }

    public function update(User $user, Workspace $workspace) {
        if ($user->role === 'admin') return true;
        return $workspace->owner_id === $user->id;
    }

    public function delete(User $user, Workspace $workspace) {
        if ($user->role === 'admin') return true;
        return $workspace->owner_id === $user->id;
    }

    public function invite($user, Workspace $workspace) {
        return $this->manage($user, $workspace);
    }

    public function manage(User $user, Workspace $workspace): bool
{
    
    if ($workspace->owner_id === $user->id) return true;

    return $workspace->members()
        ->where('user_id', $user->id)
        ->wherePivotIn('role', ['owner', 'admin'])
        ->exists();
}
}
