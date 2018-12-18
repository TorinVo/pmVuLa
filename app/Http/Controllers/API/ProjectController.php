<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Project;
use App\Option;
use App\Post;
use function GuzzleHttp\json_decode;

class ProjectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('role:Admin|Designer', ['except' => ['getProject']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        if($request->type){
            $projects = Project::with(['posts' => function($query) {
                $query->groupBy('project_id');
                $query->selectRaw('project_id, count(IF(status=\'open\', 1, null)) as open, count(IF(status=\'close\', 1, null)) as close');
            }])->latest()->get();
        }else{
            $projects = Project::with(['posts' => function($query) {
                $query->groupBy('project_id');
                $query->selectRaw('project_id, count(IF(status=\'open\', 1, null)) as open, count(IF(status=\'close\', 1, null)) as close');
            }])->latest()->paginate(10);
        }

        $projects->transform(function($project) {
            $project->percent = 100;
            $project->close = 0;
            $project->open = 0;
            if(!$project->posts->isEmpty()){
                $open = $project->posts[0]['open'];
                $close = $project->posts[0]['close'];
                $total = $open + $close;
                $percent = 100 / $total;
                $current = round(($close * $percent), 2);
                $project->percent = $current;
                $project->close = $close;
                $project->open = $open;
            }
            unset($project->posts);
            return $project;
        });

        return $projects;
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
            'name' => 'required|string|max:191|unique:projects',
            'url' => 'nullable|url',
        ]);

        return Project::create($request->all());
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
        $project = Project::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|max:191|unique:projects,name,'.$project->id,
            'url' => 'nullable|url'
        ]);
        $project->update($request->all());
        
        return ['message' => 'Updated the project info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Project = Project::findOrFail($id);
        $Project->delete();

        return ['message' => 'Project Deleted'];
    }

    public function getProject(){
        $data['filter'] = json_decode($this->findOrCreateOption(), true);
        $data['projects'] = Project::select('id', 'name')->get();
        return $data;
    }

    private function findOrCreateOption(){
        $options = [
            "dateto"=> date('m/d/Y'), 
            "datefrom" => date('m/d/Y'),
            "btnActive" => 0, 
            "hiddenClose" => false, 
            "projectSelect" => 0
        ];
        $filter = auth('api')->user()->options->where('name', 'tickets')->first();
        if(!empty($filter)){
            $options = json_decode($filter->options, true);
        }else{
            $options = Option::create([
                'name' => 'tickets',
                'options' => json_encode($options),
                'user_id' => auth('api')->user()->id,
            ]);
            $options = json_decode($options->options, true);
        }
        $oldPost = Post::where('status', 'open')->select('created_at')->oldest()->first();
        if($oldPost){
            $options['dateto'] = date('m/d/Y');
            $options['datefrom'] = $oldPost->created_at->format('m/d/Y');
        }
        return json_encode($options);
    }
}
