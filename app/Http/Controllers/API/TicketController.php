<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Project;
use App\Option;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use App\Comment;

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
        //dd($request->all());
        $idUser = auth('api')->user()->id;
        $filters = $request->only('datefrom', 'dateto', 'hiddenClose', 'projectSelect', 'projectSelect');
        $this->option->updateTincket($idUser, $filters);
        //\DB::enableQueryLog();
        return Post::with(['project' => function($query) {
            $query->select('id' ,'name');
        }])->filters($filters)->latest()->paginate(10);
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
        return Post::with(['user' => function($query) {
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

        return ['message' => 'Ticket Deleted'];
    }
}
