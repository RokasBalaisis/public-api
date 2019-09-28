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
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json([ 'message'=> $validator->errors()->first() ], 401);
        }

        if(User::where('email', $request->email)->first() != null)
            return response()->json(['message' => 'User with email ' . $request->email . ' already exists'], 303);

        $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password)]);
        return response()->json(['message' => 'User has been successfuly created', 'user' => $user], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json(['user' => $user], 200);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json([ 'message'=> $validator->errors()->first() ], 401);
        }

        if(User::find($id) === null)
            return response()->json(['message' => 'User with specified id does not exist'], 404);
        $user = User::find($id);
        $user->name = $request->name;
        $user->save();
        return response()->json(['message' => 'User information has been successfuly updated', 'user' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(User::find($id) === null)
            return response()->json(['message' => 'User with specified id does not exist'], 404);
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User has been successfuly deleted'], 200);
    }
}