<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'slug',
        'user_id',
        'article_id',
        'category_id',
        'workspace_id',
    ];

      public function getRouteKeyName() {
        return 'slug';
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
    
    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function members() {
        return $this->belongsToMany(User::class, 'user_workspace')
        ->withPivot('role')->withTimestamps();
    }
    
    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }
   
    public function articles() {
        return $this->hasMany(Article::class);
    }
    
    public function workspace(): BelongsTo {
        return $this->belongsTo(Workspace::class);
    }

    public function scopeVisibleTo($query, $user) {
    if ($user->role === 'admin') return $query;
    return $query->whereHas('workspace.members', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })->where('user_id', $user->id);
}

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($project) {
        if (empty($project->slug)) {
            $project->slug = Str::random(10);
        }
        });

        static::created(function ($project) {
            $project->update([
                'slug' => static::generateSlugWithId($project->name, $project->id),
            ]);
        });

        static::updating(function ($project) {
            if ($project->isDirty('name')) {
                $project->slug = static::generateSlugWithId($project->name, $project->id);
            }
        });
    }

    protected static function generateSlugWithId(string $name, int $id): string {
        return $id . '-' . Str::slug($name);
    }
}
