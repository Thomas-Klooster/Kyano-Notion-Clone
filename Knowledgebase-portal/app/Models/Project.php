<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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


    // protected $casts = [
    //    'created_at' => 'date:Y-m-d',
    //    'updated_at' => 'date:Y-m-d',
    // ];

      public function getRouteKeyName() {
        return 'slug';
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
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

        static::saving(function ($project) {
            $project->slug = Str::slug($project->name);
        });
    }
}
