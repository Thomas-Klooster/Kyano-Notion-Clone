<?php

namespace App\Policies;
use App\Models\User;
use App\Models\Article;


class ArticlePolicy
{
    public function before(User $user): ?bool {
        if ($user->role === 'admin') return true;
        return null;
    }

    private function inSameWorkspace(User $user, Article $article): bool {
        $workspace = $article->project?->workspace;
        if (!$workspace) return false;
        
        $member = $workspace->members()->where('user_id', $user->id)
        ->first();

        if (!$member) return false;

        if ($member->pivot->role === 'member') {
            return $article->status === 'published';
        }
        return true;
        }

    public function view(User $user, Article $article): bool {
        if (!$this->inSameWorkspace($user, $article)) return false;

        $member = $article->project->workspace->members()
            ->where('user_id', $user->id)->first();

        if ($member?->pivot->role === 'member') {
            return $article->status === 'published';
        }

        return true;
    }

    public function create(User $user, Article $article): bool {
    $workspace = $article->project?->workspace;
    if (!$workspace) return false;

    return $workspace->members()->where('user_id', $user->id)
    ->wherePivotIn('role', ['owner', 'admin'])
    ->exists();
        }

    public function update(User $user, Article $article): bool {
        return $this->create($user, $article);
    }

    public function delete(User $user, Article $article): bool {
        return $this->create($user, $article);
    }
}

