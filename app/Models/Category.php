<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug'];
    
    public function projects() {
      return
      $this->hasMany(Project::class);
    }

    public function workspace() {
      return $this->belongsTo(Workspace::class);
    }
    public function scopeVisibleTo($query, $user) {
        if ($user->role === 'admin') {
            return $query;
        }

        return $query->whereHas('projects', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}