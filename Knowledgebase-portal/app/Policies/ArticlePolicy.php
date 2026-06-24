<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    public function before(User $user): ?bool {
        return $user->role === 'admin' ? true : null;

    }

    private function workspaceMember(User $user, Article $article): ?\Illuminate\Database\Eloquent\Model {
        $workspace = $article->project?->workspace;
        if (!$workspace) return null;
        return $workspace->members()->where('user_id', $user->id)->first();
    }

    public function viewAny(User $user): bool {
        return $user->workspaces()->exists();
    }

    public function view(User $user, Article $article): bool {
        $member = $this->workspaceMember($user, $article);
        if (!$member) {
            return false;
        }

        if ($article->visibility === 'public') {
            return true;
        }

        return in_array($member->pivot->role, ['owner', 'admin']);
    }
    public function create(User $user, int $projectId): bool {
        $project = \App\Models\Project::find($projectId);
        if (!$project) return false;

        return $project->workspace->members()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', ['owner', 'admin'])
            ->exists();
    }

    public function update(User $user, Article $article): bool {
        $member = $this->workspaceMember($user, $article);
        if (!$member) return false;
        return in_array($member->pivot->role, ['owner', 'admin']);
    }

    public function delete(User $user, Article $article): bool {
        return $this->update($user, $article);
    }
}
