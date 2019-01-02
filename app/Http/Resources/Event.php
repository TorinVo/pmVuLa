<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Event extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this['id'],
            'title' => $this['name'],
            'description' => $this['description'],
            'start' => $this['start_date'],
            'end' => $this['end_date'],
            'backgroundColor' => $this['bg_color'],
            'borderColor' => $this['bg_color'],
            'event_type' => $this['event_type'],
            'repeat_type' => $this['repeat_type'],
            'repeat_interval' => $this['repeat_interval'],
            'repeat_days' => $this['repeat_days'],
            'repeat_limit' => $this['repeat_limit'],
        ];
    }
}
