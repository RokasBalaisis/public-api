<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
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
        $user = User::with('role')->find($id);
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
        if(User::find($id) === null)
        {
            return response()->json(['message' => 'User with specified id does not exist'], 404);
        }
        else
        {
            $user = User::find($id);
        }
            

        $validator = Validator::make(Input::all(), [
            'username' => ['string', 'max:50', 'unique:users'.$user->id, 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'email' => ['email', 'unique:users'.$user->id],
            'selectedRole' => ['exists:role,id'],
            'password' => ['min:6', 'alpha_dash'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            if($request->username != null)
                $user->username = $request->username;
            if($request->email != null)    
                $user->email = $request->email;
            if($request->password != null)
                $user->password = app('hash')->make($request->password);
            if($request->selectedRole != null)
                $user->role()->sync([$request->selectedRole]);
            $user->save();
            //return successful response
            return response()->json(['message' => 'User information has been successfuly updated', 'user' => $user->with('role')], 200);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User edit failed!'], 409);
        }



        
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