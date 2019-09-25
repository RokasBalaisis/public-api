<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
   /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'username' => 'required|string',
            'registration_email' => 'required|email',
            'registration_password' => 'required',
        ]);

        if(DB::table('users')->where('email', $request->input('registration_email'))->count() > 0)
        {
            return response()->json(['message' => 'User with this email already exists!'], 409);
        }

        try {
            $user = new User;
            $user->name = $request->input('username');
            $user->email = $request->input('registration_email');
            $plainPassword = $request->input('registration_password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => 2]);

            //return successful response
            return response()->json(['user' => $user, 'message' => 'User created successfuly'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }

       /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        if(DB::table('users')->where('email', $request->email)->count() > 0)
        {
            $result = DB::table('users')->where('email', $request->email)->first();
            if(DB::table('users')->where('id', $result->id)->first()->status == 1 && $result->exp > Carbon::now()->timestamp)
            {
                return response()->json(['error' => 'User is already logged in'], 401);
            }
        }
        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $payload = Auth::payload();
        DB::table('users')->where('email', $request->email)->update(['status' => 1, 'jti' => $payload['jti'], 'exp' => $payload['exp']]);
        return response()->json("User successfully logged in", 200)->header('Authorization', 'Bearer ' . $token);
    }

    
}