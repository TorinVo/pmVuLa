<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Comment;
use Carbon\Carbon;
use App\Events\MessagePosted;
use App\Image;
class CommentController extends Controller
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
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $today = Carbon::Now()->subMinutes(3);
        $message = $request->message;
        $postId = $request->ticket;
        $userId = auth('api')->user()->id;

        // preg_match_all('/##i\d+$|##i\d+\s|##\d+$|##\d+\s/', $message, $output);
        preg_match_all('/##i\d+$|##i\d+\s/', $message, $listImg);
        if(!empty($listImg[0])){
            foreach ($listImg[0] as $key => $value) {
                $temp = explode('##i', trim($value));
                if(!empty($temp)){
                    $img = Image::find($temp[1]);
                    if($img){
                        $message = str_replace(trim($value), '##vuelinki'.$img->id.'i'.substr($img->name, 1), $message);
                    }
                }
            }
        }
        preg_match_all('/##\d+$|##\d+\s/', $message, $listLink);
        if(!empty($listLink[0])){
            foreach ($listLink[0] as $key => $value) {
                $temp = explode('##', trim($value));
                $message = str_replace(trim($value), '##vuelink'.$temp[1], $message);
            }
        }
        $comment = Comment::where(['post_id'=> $postId])->latest()->first();
        if(!empty($comment) && $comment->user_id == $userId && Carbon::parse($comment->created_at) >= $today){
            $oldBody =  $comment->comments;
            $oldBody .= '<br/>'.$message;
            $comment->update(['comments' => $oldBody]);
        }else{
            $comment = auth('api')->user()->comments()->create([
                'comments' => $message,
                'post_id' => $postId,
            ]);
        }
        if(!empty($request->views))
            $comment->users()->attach($request->views)->save();
        $data = $comment->load(['user' => function($query) {
            $query->select('name', 'id', 'photo');
        }, 'users' => function($query) {
            $query->select('name', 'id');
        }]);
        broadcast(new MessagePosted($data))->toOthers();

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $last = $request->last;
        if(!empty($last)){
            return  Comment::with(['user' => function($query) {
                $query->select('name', 'id', 'photo');
            }])->where('post_id', $id)->whereRaw(
                '(id > ?  OR (created_at <> updated_at and updated_at >= ?))', [$last, Carbon::Now()->subMinutes(3)]
            )->get();
        }else{
            return  Comment::with(['user' => function($query) {
                $query->select('name', 'id', 'photo');
            }, 'users' =>  function($query) {
                $query->select('id','name');
            }])->where('post_id', $id)->get();
        }
        
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
