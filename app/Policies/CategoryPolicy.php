<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Category;

class CategoryPolicy
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

public function view(User $user, Category $category) {
    return true;
}

public function update(User $user, Category $category) {
    return true;
}

public function delete(User $user, Category $category) {
    return true;
}

}
