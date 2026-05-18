<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
    'article_id',
    'user_id',
    'helpful',
    'feedback',
    'is_read',
    ];

    protected $casts = [
    'helpful' => 'boolean',
    'is_read' => 'boolean',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
   
    public function article() {
    return $this->belongsTo(Article::class);
    }

    public function articles() {
        return $this->belongsTo(Article::class);
    }
}
