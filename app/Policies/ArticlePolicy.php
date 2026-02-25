<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Article;

class ArticlePolicy
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

public function view(User $user, Article $article) {
    // return $article->user_id === $user->id || $user->isAdmin;
    return true;
}

public function update(User $user, Article $article) {    
// return $article->user_id === $user->id || $user->isAdmin;
    return true;
}

public function delete(User $user, Article $article) {    
// return $article->user_id === $user->id || $user->isAdmin;
    return true;
}

}
