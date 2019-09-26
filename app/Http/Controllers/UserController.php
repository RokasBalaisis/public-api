<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;
use App\UserRole;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('role')->get();
        $users->makeHidden('created_at');
        return response()->json(['users' => $users], 200);
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
            'username' => ['required', 'string', 'without_spaces', 'max:50', 'unique:users', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/'],
            'role_id' => ['required'],
        ]);
        
        if ($validator->fails()) {
            return response()->json([ 'message'=> $validator->errors()->first() ], 401);
        }
        try {
            DB::table('users')->insert(['username' => $request->username, 'email' => $request->registration_email, 'password' => app('hash')->make($request->registration_password)]);
            $user = DB::table('users')->where('username', $request->username)->where('email', $request->registration_email)->first();
            DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => $request->role_idv]);

            //return successful response
            return response()->json(['user' => $user, 'message' => 'User created successfuly'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
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