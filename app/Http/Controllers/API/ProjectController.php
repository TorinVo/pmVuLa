<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Project;
use App\Option;

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
    public function index()
    { 
        return Project::latest()->paginate(10);
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
           return $options = $filter->options;
        }else{
            $options = Option::create([
                'name' => 'tickets',
                'options' => json_encode($options),
                'user_id' => auth('api')->user()->id,
            ]);
            return $options->options;
        }
    }
}
