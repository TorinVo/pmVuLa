<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name', 'url', 'describe', 'status'
    ];

    protected $casts = [
        //'status' => 'boolean'
    ];

    public function posts()
    {
        return $this->hasMany('App\Post');
    }
}
