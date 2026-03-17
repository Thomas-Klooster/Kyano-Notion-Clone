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
    'comment'
    ];


   public function users() {
    return
    $this->belongsTo(User::class);
   }
    public function article() {
        return
        $this->belongsTo(Article::class);
    }


}
