<?php
namespace App\Http\Middleware;


namespace App\Http\Middleware;
use Closure;
use App\User;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        
            $payload = \JWTAuth::manager()->getJWTProvider()->decode(\JWTAuth::getToken()->get());
            $currentuser = User::find($payload['sub']);
            var_dump(\JWTAuth::getToken()->get());
            var_dump($this->auth->guard($guard)->tokenById($payload['sub']));
            if($payload['exp'] < Carbon::now()->timestamp)
            {
                if(DB::table('users')->where('id', $currentuser->id)->first()->jti == $payload['jti'])
                {
                    DB::table('users')->where('id', $currentuser->id)->update(['status' => 0, 'jti' => null]);
                }
                return response('Token is Expired.', 401);
            }
            
            if($currentuser == null)
                return response()->json('Unauthorized', 401);
            if(DB::table('users')->where('id', $currentuser->id)->first()->status == 0)
            {
                return response()->json('Unauthorized', 401);
            }
            if (! $new_token = $this->auth->guard($guard)->fromUser($currentuser)) {
                return response()->json('Unauthorized', 401);
            }
            $new_payload = \JWTAuth::manager()->getJWTProvider()->decode($new_token);
            DB::table('users')->where('id', $currentuser->id)->update(['jti' => $new_payload['jti']]);
            return $next($request)->header("Authorization", "Bearer " . $new_token);
         


    }
    
}


// catch (JWTException $e) {
//     if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
//         return response("Token is Invalid.");
//     }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
//         return response('Token is Expired.');
//     }else{
//         return response('Unauthorized.', 401);
//     }