<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Image;

class ImageController extends Controller
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
        $type = '.' . explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
        
        $image = Image::create([
            'name' => $type,
            'post_id' => $request->post_id
        ]);
        
        \Image::make($request->photo)->save(public_path('img/attach/').($image->id.$type));
        
        $image->name = asset(
            'img/attach/' . $image->id . $image->name
        );
        return $image;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $collection = Image::where('post_id', $id)->get();

        $collection->transform(function($image) {
            $image->name = asset(
                'img/attach/' . $image->id . $image->name
            );
            return $image;
        });

        return $collection;
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
        $image = Image::findOrFail($id);
        $name = $image->id.$image->name;
        $image->delete();

        $oldPhoto = public_path('img/attach/').$name;
        if(file_exists($oldPhoto)){
            @unlink($oldPhoto);
        }

        return ['message' => 'Image Deleted'];
    }
}
