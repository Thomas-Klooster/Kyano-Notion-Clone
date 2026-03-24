<?php

namespace App\Policies;
  use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
public function viewAny($user)
{
    return true;
}

public function view($user, Category $category)
{
    return true;
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
