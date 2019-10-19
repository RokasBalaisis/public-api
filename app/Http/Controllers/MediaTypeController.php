<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\Category;
use App\MediaType;

class MediaTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media_types = MediaType::with('media')->get();
        return response()->json(['media_types' => $media_types], 200);
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
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z]+$/']
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $media_type = new MediaType();
        $media_type->name = $request->name;
        $media_type->save();
        return response()->json(['message' => 'Media type has been successfuly created', 'media_type' => $media_type], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(MediaType::find($id) === null)
        return response()->json(['message' => 'Media type with specified id does not exist'], 404);
        $media_type = MediaType::find($id);
        return response()->json(['media_type' => $media_type], 200);
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
        if(MediaType::find($id) === null)
            return response()->json(['message' => 'Media type with specified id does not exist'], 404);
        $media_type = MediaType::find($id);
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:3', 'regex:/^[A-Za-z]+$/']
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $media_type->name = $request->name;
        $media_type->save();
        return response()->json(['message' => 'Media type has been successfuly updated', 'media_type' => $media_type], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(MediaType::find($id) === null)
            return response()->json(['message' => 'Media type with specified id does not exist'], 404);
        if(DB::table('media')->where('media_type_id', $id)->count() > 0)
            return response()->json(['message' => 'Cannot delete media type with existing media owning it'], 422);
        $media_type = MediaType::find($id);
        $media_type->delete();
        return response()->json(['message' => 'Media type has been successfuly deleted'], 200);
    }
}