<?php

namespace App\Models;
use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;
use Illuminate\Support\Str;

class Article extends Model
{

    use HasFactory, HasTags;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'summary',
        'status',
        'slug',
        'project_id',
        'category_id',
        'workspace_id',
        'visibility',
    ];

    protected $casts = [
        'visibility' => 'string',
        'status' => 'string'
        ];


        public function getRouteKeyName() {
        return 'slug';
    }


        public function users() {
            return $this->belongsTo(User::class);
        }

        public function workspace() {
            return $this->belongsTo(Workspace::class);
    }
        public function categories() {
            return $this->belongsTo(Category::class);
    }

        public function category() {
            return $this->belongsTo(Category::class);
    }

    public function project() {
            return $this->belongsTo(Project::class);
        }
        public function attachments() {
            return $this->hasMany(Attachment::class);
    }

        public function feedback() {
            return $this->hasMany(Feedback::class);
    }

        public function feedbacks() {
            return $this->hasMany(Feedback::class);
    }

        public function tags() {
            return $this->morphToMany(Tag::class, 'taggable');
}

public function syncTags(array $tags): void
{
    $tagIds = collect($tags)->map(function ($name) {
        return Tag::firstOrCreate(['name' => $name])->id;
    });

    $this->tags()->sync($tagIds);
}

public function scopeVisibleTo($query, $user) {
    if ($user->role === 'admin') return $query;
    return $query->whereHas('project.workspace.members', function ($q) use ($user) {
    $q->where('user_id', $user->id);
})->where(function ($q) use ($user) {
    $q->where('visibility', 'public')
    ->orWhere('user_id', $user->id);
});
}



        protected static function boot(): void
    {
        parent::boot();
        

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::random(10);
            }
        });

        static::created(function ($article) {
            $article->update([
                'slug' => static::generateSlugWithId($article->title, $article->id),
            ]);
        });

        static::updating(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = static::generateSlugWithId($article->title, $article->id);
            }
        });

    }

    protected static function generateSlugWithId(string $title, int $id): string {
        return $id . '-' . Str::slug($title);
    }
}
