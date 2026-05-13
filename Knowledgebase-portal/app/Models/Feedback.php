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
    'feedback'
    ];

    protected $casts = [
       'created_at' => 'date:Y-m-d',
       'updated_at' => 'date:Y-m-d',
    ];



   public function user() {
   return $this->belongsTo(User::class);
   }
   
    public function article() {
    return $this->belongsTo(Article::class);
    }


}
