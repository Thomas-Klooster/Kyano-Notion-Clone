<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'owner_id'];

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members() {
        return $this->belongsToMany(User::class, 'user_workspace')
        ->withPivot('role')->withTimestamps(); 
    }
    public function categories() {
        return $this->hasMany(Category::class);
    }
    public function articles() {
        return
        $this->hasMany(Article::class);
    }
        public function projects() {
        return
        $this->hasMany(Project::class);
    }

    public function scopeVisibleTo($query, $user)
{
    if ($user->role === 'admin') return $query;

    return $query->where(function ($q) use ($user) {
        $q->where('owner_id', $user->id)   
          ->orWhereHas('members', function ($q) use ($user) {
              $q->where('user_id', $user->id);
          });
    });
}

}
