<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Article;
use App\Models\Project;
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
public function create(User $user, $projectId)
{

    $project = Project::find($projectId);
    if (!$project) return false;
    dd($user->id, $projectId, Project::find($projectId)?->user_id);
    return $project->user_id === $user->id;
    // $workspace = Workspace::find($workspaceId);
    // if (!$workspace) return false;

    // return $workspace->members()
    //     ->where('user_id', $user->id)
    //     ->exists();
}
public function update(User $user)
{
    return in_array($user->role, ['admin', 'owner']);

// $workspace = $article->workspace;

    // if (!$workspace) return false;

    // return $workspace->members()->where('user_id', $user->id)->exists();
}
public function delete(User $user)
{
        return in_array($user->role, ['admin', 'owner']);


    // $workspace = $article->workspace;
    // if (!$workspace) return false;
    // return
    // $workspace->members()->where('user_id', $user->id)->exists();
}
    

}
