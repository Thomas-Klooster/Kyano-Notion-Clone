<?php

namespace App\Policies;
use App\Models\Category;
use App\Models\User;
use App\Models\Workspace;

class CategoryPolicy
{
public function viewAny(User $user)
{
    return in_array($user->role, ['admin', 'owner']);

}

public function view(User $user, Category $category) {

    return in_array($user->role, ['admin', 'owner']) ||
    $category->workspace->members->contains($user->id);
    }

public function create(User $user)
    {
        return in_array($user->role, ['admin', 'owner']);
    }

public function update(User $user)
{
    return in_array($user->role, ['admin', 'owner']);
    
}

public function delete(User $user)
{
    return in_array($user->role, ['admin', 'owner']);
}

}
