<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\Actor;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actors = Actor::all();
        return response()->json(['actors' => $actors], 200);
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
            'name' => ['required', 'min:3', 'alpha'],
            'surname' => ['required', 'min:3', 'alpha'],
            'born' => ['required', 'date'],
            'info' => ['required'],
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $actor = new Actor;
        $actor->name = $request->name;
        $actor->surname = $request->surname;
        $actor->born = $request->born;
        $actor->info = $request->info;
        $actor->save();
        return response()->json(['message' => 'Actor has been successfuly created', 'actor' => $actor], 200);
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
        if(DB::table('user_role')->where('role_id', $id)->count() > 0)
            return response()->json(['message' => 'Cannot delete role with existing users owning it'], 422);
        $role = Role::find($id);
        $role->delete();
        return response()->json(['message' => 'Role has been successfuly deleted'], 200);
    }
}