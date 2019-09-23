<?php
namespace App\Http\Middleware;


namespace App\Http\Middleware;
use Closure;
use App\User;
use Illuminate\Contracts\Auth\Factory as Auth;
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
        
        try
        {
            $payload = \JWTAuth::manager()->getJWTProvider()->decode(\JWTAuth::getToken()->get());
            if($payload['exp'] < Carbon::now()->timestamp)
            {
                return response('Token is Expired.', 401);
            }
            $currentuser = User::find($payload['sub']);
            $this->auth->guard($guard)->logout();
            if (! $new_token = $this->auth->guard($guard)->fromUser($currentuser)) {
                return response()->json('attempt error', 401);
            }
            return $next($request)->header("Authorization", "Bearer " . $new_token);
        }
            catch (JWTException $e)
            {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return response("Token is Invalid.", 401);
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return response('Token is Expired.', 401);
                }
            }   
            return response('Unauthorized.', 401);

        return $next($request);
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