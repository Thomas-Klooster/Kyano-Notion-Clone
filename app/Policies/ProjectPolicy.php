<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{

       
    // public function viewAny(User $user, Project $project) {
    // return $project->users()->where('user_id', $user->id)->exists() ||
    // $user->role === 'admin';
    // }
public function view(User $user, Project $project)
{
    return $project->user_id === $user->id ||
    $user->role == 'admin';
}
public function create(User $user)
{
    // $project = Project::find($projetcId);
    // if (!$project) return false;
    // return $project->user_id === $user->id;
    return in_array($user->role, ['admin', 'owner']);
}

public function update(User $user)
{
    return in_array($user->role, ['admin', 'owner']);
}

public function delete($user)
{
    return in_array($user->role, ['admin', 'owner']);
}
}