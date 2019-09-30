<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Role;
use App\Media;
use Carbon\Carbon;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media = Media::all();
        return response()->json(['media' => $media], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z]+$/'],
            'short_description' => ['required'],
            'description' => ['required'],
            'trailer_url' => ['regex:www.youtube(?:-nocookie)?.com\/(?:v|embed)\/([a-zA-Z0-9-_]+).*'],
            'image' => ['required', 'array', 'min:3', 'max:3'],
            'image.*' => ['required','file','mimes:jpg,jpeg,png,bmp'],
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $media = new Media;
        $media->name = $request->name;
        $media->short_description = $request->short_description;
        $media->description = $request->description;
        $media->trailer_url = $request->trailer_url;
        $media->save();

        $file_data = array();

        $counter = 0;
        foreach($request->image as $image)
        {
            array_push($file_data, array(['media_id' => $media->id, 'folder' => 'images', 'name' => 'image['.$counter.'].'.$image->getClientOriginalExtension(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]));
            $image->storeAs('media/'.$media->id.'/images', 'image['.$counter.'].'.$image->getClientOriginalExtension());
            $counter++;
        }
        DB::table('media_files')->insert($file_data);
        return response()->json(['message' => 'Media has been successfully created', 'media' => $media], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Role::find($id) === null)
        return response()->json(['message' => 'Role with specified id does not exist'], 404);
        $role = Role::find($id);
        return response()->json(['role' => $role], 200);
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
        if(Role::find($id) === null)
            return response()->json(['message' => 'Role with specified id does not exist'], 404);
        $role = Role::find($id);
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z]+$/']
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $role->name = $request->name;
        $role->save();
        return response()->json(['message' => 'Role has been successfuly updated', 'role' => $role], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Role::find($id) === null)
            return response()->json(['message' => 'Role with specified id does not exist'], 404);
        $role = Role::find($id);
        $role->delete();
        return response()->json(['message' => 'Role has been successfuly deleted'], 200);
    }
}