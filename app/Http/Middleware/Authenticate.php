<?php
namespace App\Http\Middleware;


namespace App\Http\Middleware;
use Closure;
use App\User;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
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
        if($this->auth->guard($guard)->guest())
            return response()->json(['message' => 'Unauthorized'], 401);
        $payload = \JWTAuth::manager()->getJWTProvider()->decode(\JWTAuth::getToken()->get());
        $currentToken = \JWTAuth::getToken()->get();
            
            $currentuser = User::find($payload['sub']);
            if($payload['exp'] < Carbon::now()->timestamp)
            {
                return response(['message' => 'Token is Expired.'], 401);
            }
            
            if($currentuser == null)
            return response()->json(['message' => 'Unauthorized'], 401);
            if(Carbon::now()->timestamp - $payload['iat'] <= 1800){
                return $next($request);
            }
            else
            {
                if (! $new_token = $this->auth->guard($guard)->fromUser($currentuser)) {
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
                \JWTAuth::invalidate($currentToken);
                $new_payload = \JWTAuth::manager()->getJWTProvider()->decode($new_token);
                DB::table('users')->where('id', $currentuser->id)->update(['exp' => $new_payload['exp']]);
                return $next($request)->header("Authorization", "Bearer " . $new_token);
            }

    }
    
}
