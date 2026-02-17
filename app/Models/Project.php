<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'projectname',
        'description',
        'category_id'
    ];


    public function category() {
        return
        $this->belongsTo(Category::class);
    }
}


