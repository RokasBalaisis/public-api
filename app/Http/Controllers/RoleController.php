<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles], 200);
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
            return response()->json([ 'message'=> $validator->errors()->first() ], 422);
        }

        $role = new Role;
        $role->name = $request->name;
        $role->save();
        return response()->json(['message' => 'Role has been successfuly created', 'role' => $role], 200);
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
            return response()->json([ 'message'=> $validator->errors()->first() ], 422);
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
        return response()->json(['message' => 'User has been successfuly deleted'], 200);
    }
}