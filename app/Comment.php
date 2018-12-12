<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
    protected $fillable = [
        'comments', 'views', 'post_id', 'user_id'
    ];

    protected $casts = [
        'views' => 'json'
    ];

    public function post()
    {
        return $this->belongsTo('App\Post', 'post_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function users()
    {
        return $this->belongsToJson('App\User', 'views');
    }
}
