<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;
use App\Events\NewMessage;

class MesageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = Message::create([
            'from' => auth('api')->user()->id,
            'to' => $request->contact_id,
            'text' => $request->text
        ]);

        $message->load(['fromContact' => function($query) {
            $query->select('id' ,'name', 'photo');
        }]);
        
        $isMine = ($message->from == auth('api')->user()->id) ? true : false;
        $message->name = $message->fromContact->name;
        $message->photo = $message->fromContact->photo;
        $message->photo = asset('img/profile/' . $message->photo);
        $message->isMine = $isMine;
        unset($message->fromContact);

        broadcast(new NewMessage($message->toArray()));

        return $message;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $idUser = auth('api')->user()->id;
        Message::where('from', $id)->where('to', $idUser)->update(['read' => true]);
        $messages = Message::with(['fromContact' => function($query) {
            $query->select('id' ,'name', 'photo');
        }])->where(function($q) use ($id, $idUser) {
            $q->where('from',  $idUser);
            $q->where('to', $id);
        })->orWhere(function($q) use ($id, $idUser) {
            $q->where('from', $id);
            $q->where('to',  $idUser);
        })->get();
        $messages->transform(function($message) use ($idUser) {
            $isMine =  ($message->from == $idUser) ? true : false;
            $message->name_to = $message->fromContact->name;
            $message->name = $message->fromContact->name;
            $message->photo = $message->fromContact->photo;
            $message->photo = asset('img/profile/' . $message->photo);
            $message->isMine = $isMine;
            unset($message->fromContact);
            return $message;
        });
        return $messages;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
