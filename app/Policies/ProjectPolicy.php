<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{
    /**
     * Create a new policy instance.
     */

    public function before(User $user) {
    if ($user->isAdmin()) {
        return true;
    }
    return null;
}

public function view(User $user, Project $project) {
    // return $project->user_id === $user->id || $user->isAdmin;
    return true;
    }

public function update(User $user, Project $project) {
    // return $project->user_id === $user->id || $user->isAdmin;
    return true;
    }

public function delete(User $user, Project $project) {
    // return $project->user_id === $user->id || $user->isAdmin;
    return true;
    }

}
