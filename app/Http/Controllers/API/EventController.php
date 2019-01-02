<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Event;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;
use App\Http\Resources\Event as EventResource;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start = $request->start;
        $end = $request->end;
        $events = $this->get_events($start, $end, '1');
        return EventResource::collection(new collection($events));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'description' => 'required|string|max:191',
            'start' => 'date_format:"Y-m-d H:i"|date|required',
            'end' => 'date_format:"Y-m-d H:i"|date|after:start|required',
            'repeat_interval' => 'required_if:repeat,true,1',
            'repeat_limit' => "event_repeat_limit:{$request->repeat},{$request->repeat_end}",
        ]);

        $event = new Event;

        $event->user_id = auth('api')->user()->id;
        $event->name = $request->title;
        $event->description = $request->description;
        $event->start_date = Carbon::parse($request->start)->toDateTimeString();
        $event->end_date = Carbon::parse($request->end)->toDateTimeString();
        $event->bg_color = $request->backgroundColor;
        $event->repeat_type = ($request->repeat)?$request->repeat_type:null;
        $event->repeat_interval = ($request->repeat)?$request->repeat_interval:null;
        $event->repeat_days = ($request->repeat)?Carbon::parse($request->start)->dayOfWeek:null;
        $event->repeat_limit = ($request->repeat && $request->repeat_end)?$request->repeat_limit:null;
        $event->save();

        return $event;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'description' => 'required|string|max:191',
            'start' => 'date_format:"Y-m-d H:i"|date|required',
            'end' => 'date_format:"Y-m-d H:i"|date|after:start|required',
            'repeat_interval' => 'required_if:repeat,true,1',
            'repeat_limit' => "event_repeat_limit:{$request->repeat},{$request->repeat_end}",
        ]);
        


        $event->name = $request->title;
        $event->description = $request->description;
        $event->start_date = Carbon::parse($request->start)->toDateTimeString();
        $event->end_date = Carbon::parse($request->end)->toDateTimeString();
        $event->bg_color = $request->backgroundColor;
        $event->repeat_type = ($request->repeat)?$request->repeat_type:null;
        $event->repeat_interval = ($request->repeat)?$request->repeat_interval:null;
        $event->repeat_days = ($request->repeat)?Carbon::parse($request->start)->dayOfWeek:null;
        $event->repeat_limit = ($request->repeat && $request->repeat_end)?$request->repeat_limit:null;
        $event->save();
        return ['message' => 'Updated the user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return ['message' => 'Event Deleted'];
    }

    public function get_events($date_from, $date_to, $calendar_type)
    {

        $list = array();

        $list = Event::whereDate('start_date', '>=', $date_from)
            ->whereDate('end_date', '<=', $date_to)
            ->where('user_id', auth('api')->user()->id)->get()->toArray();
        // while ($events = db_fetch_array($events_query)) {
        //     $list[] = $events;
        // }

        if (count($repeat_events_list = $this->get_repeat_events($date_to, $calendar_type))) {
            $list = array_merge($list, $repeat_events_list);
        }

        return $list;
    }

    public function get_repeat_events($date_to, $calendar_type)
    {
        //convert date to timestamp
        $date_to_timestamp = Carbon::parse($date_to)->timestamp;
        
        $list = array();

        //get all events that already started (start_date<=date_to)
        $data = Event::whereNotNull('repeat_type')
            ->whereDate('start_date', '<=', $date_to)
            ->where('user_id', auth('api')->user()->id)->get()->toArray();
            // $events_query = db_query("select * from app_ext_calendar_events where length(repeat_type)>0 and FROM_UNIXTIME(start_date,'%Y-%m-%d')<='" . $date_to . "' and  event_type='" . $calendar_type . "' and users_id='" . db_input($app_user['id']) . "'");
        foreach ($data as $key => $events) {
            $start_date = Carbon::parse($events['start_date'])->timestamp;
            //set repeat end      
            $repeat_end = false;
            if ($events['repeat_end'] > 0) {
                $repeat_end = $events['repeat_end'];
            } 

            //get repeat events by type                       
            switch ($events['repeat_type']) {
                case 'daily': 
                    //check repeat events day bay day
                    for ($date = $start_date; $date <= $date_to_timestamp; $date += 86400) {
                        if ($date > $start_date) {
                            $dif = round(abs($date - $start_date) / 86400);
                            if ($dif > 0) {
                                $event_obj = $events;
                                $event_obj['start_date'] = Carbon::parse($event_obj['start_date'])->addDay($dif)->toDateTimeString();
                                $event_obj['end_date'] = Carbon::parse($event_obj['end_date'])->addDay($dif)->toDateTimeString();
                                if ($this->check_repeat_event_dif($dif, $event_obj, $repeat_end)) {
                                    $list[] = $event_obj;
                                }
                            }
                        }
                    }
                    break;
                case 'weekly':  
                    for ($date = $start_date; $date <= $date_to_timestamp; $date += 86400) {
                        if ($date > $start_date) {
                            $dif = round(abs($date - $start_date) / 86400);
                            //$week_dif = $this->weeks_dif($start_date, $date);
                            //$week_dif2 = $this->weeks_dif($start_date, $date);
                            $start = Carbon::createFromTimestamp($start_date);
                            $end = Carbon::createFromTimestamp($date);
                            $week_dif = $end->diffInWeeks($start);
                            if($dif > 0 && (in_array(date('N', $date), explode(',', $events['repeat_days'])))){
                                $event_obj = $events;
                                $event_obj['start_date'] = Carbon::parse($event_obj['start_date'])->addDay($dif)->toDateTimeString();
                                $event_obj['end_date'] = Carbon::parse($event_obj['end_date'])->addDay($dif)->toDateTimeString();

                                if ($this->check_repeat_event_dif($week_dif, $event_obj, $repeat_end)) {
                                    $list[] = $event_obj;
                                }
                            }
                        }
                    }

                    break;
                case 'monthly':
                    //check 1                                      
                    $date_to_timestamp2 = strtotime('-2 month', $date_to_timestamp);
                    $dif = $this->months_dif_2($start_date, $date_to_timestamp2);
                    if ($dif > 0) {
                        $event_obj = $events;
                        $event_obj['start_date'] = Carbon::parse($event_obj['start_date'])->addMonths($dif)->toDateTimeString();
                        $event_obj['end_date'] = Carbon::parse($event_obj['end_date'])->addMonths($dif)->toDateTimeString();

                        if ($this->check_repeat_event_dif($dif, $event_obj, $repeat_end)) {
                            $list[] = $event_obj;
                        }
                    }

                    // //check 2
                    $date_to_timestamp1 = strtotime('-1 month', $date_to_timestamp);
                    $dif = $this->months_dif_2($start_date, $date_to_timestamp1);
                    if ($dif > 0) {
                        $event_obj = $events;
                        $event_obj['start_date'] = Carbon::parse($event_obj['start_date'])->addMonth($dif)->toDateTimeString();
                        $event_obj['end_date'] = Carbon::parse($event_obj['end_date'])->addMonth($dif)->toDateTimeString();

                        if ($this->check_repeat_event_dif($dif, $event_obj, $repeat_end)) {
                            $list[] = $event_obj;
                        }
                    }

                    //check 3
                    $dif = $this->months_dif_2($start_date, $date_to_timestamp);
                    if ($dif > 0) {
                        $event_obj = $events;
                        $event_obj['start_date'] = Carbon::parse($event_obj['start_date'])->addMonth($dif)->toDateTimeString();
                        $event_obj['end_date'] = Carbon::parse($event_obj['end_date'])->addMonth($dif)->toDateTimeString();

                        if ($this->check_repeat_event_dif($dif, $event_obj, $repeat_end)) {
                            $list[] = $event_obj;
                        }
                    }

                    break;
                case 'yearly':
                    $dif = date('Y', $date_to_timestamp) - date('Y', $start_date);

                    if ($dif > 0) {
                        $events['start_date'] = Carbon::parse($events['start_date'])->addYears($dif)->toDateTimeString();
                        $events['end_date'] = Carbon::parse($events['end_date'])->addYears($dif)->toDateTimeString();

                        if ($this->check_repeat_event_dif($dif, $events, $repeat_end)) {
                            $list[] = $events;
                        }
                    }
                    break;
            }

        }

        return $list;

    }

    public function months_dif_2($start, $end){
        $date1 = new DateTime(date('Y-m-d', $start));
        $date2 = new DateTime(date('Y-m-d', $end));

        $diff = $date1->diff($date2);
        return ($diff->format('%y') * 12) + $diff->format('%m');
    }

    public function check_repeat_event_dif($dif, $events, $repeat_end)
    {
        $check = true;
        //dd($events);
        if ($dif > 0) {
            //check interval
            if ($dif / $events['repeat_interval'] != floor($dif / $events['repeat_interval'])) $check = false;            

            //check repeat limit
            if ($events['repeat_limit'] > 0)
                if (floor($dif / $events['repeat_interval']) > $events['repeat_limit']) $check = false;
        } else {
            $check = false;
        } 

        //check repeat end date            
        if ($repeat_end > 0) {
            if ($repeat_end < $events['start_date']) {
                $check = false;
            }
        }

        return $check;
    }
}
