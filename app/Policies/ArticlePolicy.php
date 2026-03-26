<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{

public function view(User $user, Article $article) {
    // return $article->project->user_id === $user->id;
    if (!$article->project)
    return false;
    
     return
     $article->project->user_id === $user->id;
    }
    
public function before($user) {
     if ($user->role === 'admin') {
         return true;
     }
 }
public function create(User $user)
{
    //  return in_array($user->role, ['admin', 'owner']);
    //   $article = Article::find($articleId);
    //   if (!$article) return false;
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

