<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Role;
use App\UserRole;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;

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
            'username' => ['required', 'string', 'max:50', 'unique:users', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
            'role_id' => ['required'],
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if(!DB::table('roles')->where('id', '=', $request->role_id)->exists()){
            return response()->json(['role_id' => ['Selected role does not exist!']], 422);
        }
        try {
            DB::table('users')->insert(['username' => $request->username, 'email' => $request->email, 'password' => app('hash')->make($request->password), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            $user = DB::table('users')->where('username', $request->username)->where('email', $request->email)->first();
            DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => $request->role_id]);

            $user = User::with('role')->find($user->id);
            //return successful response
            return response()->json(['user' => $user, 'message' => 'User created successfuly'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User creation Failed!'], 409);
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
        if(User::find($id) === null)
            return response()->json(['message' => 'User with specified id does not exist'], 404);
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
        if(User::with('role')->find($id) === null)
            return response()->json(['message' => 'User with specified id does not exist'], 404);

            

        $validator = Validator::make($request->all(), [
            'username' => ['string', 'max:50', 'unique:users,username,'. $id, 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'email' => ['email', 'unique:users,email,'. $id],
            'role_id' => ['exists:roles,id'],
            'password' => ['min:6', 'alpha_dash'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if(!DB::table('roles')->where('id', '=', $request->role_id)->exists() && $request->has('role_id')){
            return response()->json(['role_id' => ['Selected role does not exist!']], 422);
        }
        try {
            $user = User::with('role')->find($id);
            if($request->username != null)
                $user->username = $request->username;
            if($request->email != null)    
                $user->email = $request->email;
            if($request->password != null)
                $user->password = app('hash')->make($request->password);
            if($request->role_id != null)
                $user->role()->sync([$request->role_id]);
            $user->save();
            $user = User::with('role')->find($id);
            //return successful response
            return response()->json(['message' => 'User information has been successfuly updated', 'user' => $user], 200);

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
        DB::table('user_role')->where('user_id', $id)->delete();
        $user->delete();
        return response()->json(['message' => 'User has been successfuly deleted'], 200);
    }
}