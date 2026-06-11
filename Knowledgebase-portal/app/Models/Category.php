<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'workspace_id'];

    
    public function projects() {
      return
      $this->hasMany(Project::class);
    }

    public function workspace() {
      return $this->belongsTo(Workspace::class);
    }

    public function articles() {
    return $this->hasMany(Article::class);
    }


    public function getRouteKeyName() {
        return 'slug';
    }

    public function scopeVisibleTo($query, $user) {
    if (!$user) return $query->whereRaw('1 = 0');
    if ($user->role === 'admin') return $query;
    return $query->whereHas('projects.workspace.members', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    });
}
   protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::random(10);
            }
        });

        static::created(function ($category) {
            $category->update([
                'slug' => static::generateSlugWithId($category->name, $category->id),
            ]);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = static::generateSlugWithId($category->name, $category->id);
            }
        });
    }

    protected static function generateSlugWithId(string $name, int $id): string
    {
        return $id . '-' . Str::slug($name);
    }
}