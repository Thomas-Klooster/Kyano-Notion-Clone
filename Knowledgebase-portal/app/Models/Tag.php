<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;


class Tag extends Model 
{

    use HasFactory;

    protected $fillable = ['name'];

    public function taggables() {
        return $this->morphedByMany(Article::class, 'taggable');
    }

    public function articles() {
        return $this->morphedByMany(Article::class, 'taggable');
    }

}