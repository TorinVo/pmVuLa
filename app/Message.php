<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'from', 'to', 'text', 'read'
    ];
    public function fromContact()
    {
        return $this->hasOne(User::class, 'id', 'from');
    }
}
