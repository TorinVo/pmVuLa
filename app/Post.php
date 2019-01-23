<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Post extends Model
{
    protected $fillable = [
        'title', 'project_id', 'user_id', 'status', 'reviewed', 'priority', 'description'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function scopeFilters($query, $filters)
    {
        //dd($filters);
        if (is_array($filters)) {
            if(!empty($filters['dateto']) && !empty($filters['datefrom'])){
                $dateS = Carbon::createFromFormat('m/d/Y', $filters['datefrom'])->format('Y-m-d 00:00:00');
                $dateE = Carbon::createFromFormat('m/d/Y', $filters['dateto'])->format('Y-m-d 23:59:59');
                $query->whereBetween('created_at', [$dateS, $dateE]);
            }
            
            if(!empty($filters['projectSelect'])){
                $query->where('project_id', $filters['projectSelect']);
            }

            if(!empty($filters['hiddenClose']) && $filters['hiddenClose'] == 'true'){
                $query->where('status', 'open');
            }

            
            return $query;
        }

        return $query;
    }
}
