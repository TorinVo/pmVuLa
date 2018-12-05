<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = [
        'name', 'options', 'user_id'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function getTincket($id)
    {
        $options = [
            "dateto"=> date('m/d/Y'), 
            "datefrom" => date('m/d/Y'), 
            "btnActive" => 0, 
            "hiddenClose" => false, 
            "projectSelect" => 0
        ];
        $filter = static::where([
            'name' => 'tickets',
            'user_id' => $id
        ])->first();
        if(!empty($filter)){
           return $options = $filter->options;
        }else{
            $options = static::create([
                'name' => 'tickets',
                'options' => json_encode($options),
                'user_id' => $id,
            ]);
            return $options->options;
        }
    }

    public function updateTincket($idUser, $data=''){
        $tickets = static::where([
            'name' => 'tickets',
            'user_id' => $idUser
        ])->first();
        if(!empty($tickets) && !empty($data)){
            $tickets->options = json_encode($data);
            $tickets->save();
        }
    }
}
