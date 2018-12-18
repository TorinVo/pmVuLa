<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Project;
use App\Option;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use App\Comment;
use App\Events\MessageNotification;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['showWeb']]);
        $this->option = new Option();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $idUser = auth('api')->user()->id;
        $filters = $request->only('datefrom', 'dateto', 'hiddenClose', 'projectSelect', 'projectSelect');
        $this->option->updateTincket($idUser, $filters);
        //\DB::enableQueryLog();
        return Post::with(['project' => function($query) {
            $query->select('id' ,'name');
        }])->select('posts.*', \DB::raw('(SELECT count(cm.id) FROM comments cm WHERE (NOT JSON_CONTAINS(cm.views, \''.$idUser.'\') OR cm.views IS NULL) AND cm.post_id = posts.id) AS comments'))->filters($filters)->latest()->paginate(10);
        //dd(\DB::getQueryLog());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title' => 'required|string|max:191',
            'project_id' => 'required|numeric|exists:projects,id',
        ]);
        $user_id = auth('api')->user()->id;
        $request->merge(['user_id' => $user_id]);
        broadcast(new MessageNotification(['type'=> 'ticketOpen', 'message'=> 'open']));
        return Post::create($request->all());
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
        $comments = Comment::where('post_id', $id)->whereRaw('NOT JSON_CONTAINS(views, \''.$idUser.'\') OR views IS NULL')->get();
        foreach ($comments as $key => $comment) {
            $comment->users()->attach([$idUser])->save();
        }
        //dd($comments);
        return Post::with(['user' => function($query) {
            $query->select('name', 'id');
        }, 'project' => function($query) {
            $query->select('name', 'id');
        }])->findOrFail($id);
    }

    public function showWeb($id){
        return view('home', ['data' => $id]);
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
        $ticket = Post::findOrFail($id);
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'project_id' => 'required|numeric|exists:projects,id',
        ]);
        $ticket->update($request->all());
        
        return ['message' => 'Updated the ticket info'];
    }

    public function getOpenTicket(){
        return Post::where('status', 'open')->count();
    }

    public function getMyTicket(){
        $idUser = auth('api')->user()->id;
        return Post::where('user_id', $idUser)->count();
    }

    public function updateActions(Request $request)
    {
        $ticket = Post::findOrFail($request->id);
        switch ($request->type) {
            case 'reviewed':
                $ticket->reviewed = 1;
                break;
            case 'open':
                $ticket->status = 'open';
                broadcast(new MessageNotification(['type'=> 'ticketOpen', 'message'=> 'open']));
                break;
            default:
                $ticket->status = 'close';
                broadcast(new MessageNotification(['type'=> 'ticketOpen', 'message'=> 'close']));
                break;
        }
        $ticket->update();
        return $ticket;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        broadcast(new MessageNotification(['type'=> 'ticketOpen', 'message'=> 'close']));
        return ['message' => 'Ticket Deleted'];
    }
}
