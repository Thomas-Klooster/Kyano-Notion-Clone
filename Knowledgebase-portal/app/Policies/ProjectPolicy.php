<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Project;

class ProjectPolicy
{


    public function before(User $user): ?bool {
    if ($user->role === 'admin') return true;
    return null;
}
    private function workspaceMember(User $user, Project $project): ?\Illuminate\Database\Eloquent\Model {
    $workspace = $project->workspace;
    if (!$workspace) return null;
    return $workspace->members()->where('user_id', $user->id)->first();
}

    public function view(User $user, Project $project): bool {
    if ($this->workspaceMember($user, $project) === null) {
        return false;
    }

    return $project->user_id === $user->id;
    }

    public function create(User $user, Project $project): bool {
    $member = $this->workspaceMember($user, $project);
    if (!$member) return false;
    return in_array($member->pivot->role, ['owner', 'admin', 'member']);
}

    public function update(User $user, Project $project): bool {
    return $this->create($user, $project);
}

    public function delete(User $user, Project $project): bool {
     $member = $this->workspaceMember($user, $project);
     if (!$member) return false;
     return $member->pivot->role === 'owner';
}
}