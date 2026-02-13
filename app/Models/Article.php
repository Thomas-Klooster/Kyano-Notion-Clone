<?php

namespace App\Models;
use Carbon\Traits\Timestamp;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
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
        'visibility',

    ];

    //    public function category() {
    //     return $this->belongsTo(Category::class);
        
    // }
    public $timestamps = true;
}
