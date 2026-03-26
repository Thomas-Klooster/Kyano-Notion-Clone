<?php

namespace App\Policies;
use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
public function viewAny(User $user, Category $category)
{
    return 
    $category->user_id === $user->id;
}

public function view(User $user) {
    return in_array($user->role, ['admin', 'owner']);
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
