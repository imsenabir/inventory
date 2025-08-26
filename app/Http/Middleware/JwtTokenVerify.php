<?php

namespace App\Http\Middleware;

use App\Helpers\JwtToken;
use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JwtTokenVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            if(!$request->cookie('token')){
                if($request->expectsJson()){
                    return response()->json([
                        'message' => 'Unauthenticated',
                    ],401);
                }
                return redirect()->route('login');
            }
            $decode = JwtToken::verifyToken($request->cookie('token'));
            Log::info(json_encode($decode));
            if($decode['error']){
                return response()->json([
                    'message' => 'Something went wrong',
                ]);
            }

            $payload = $decode['payload'];
            $user = User::where('id', $payload->id)
                ->where('email', $payload->email)
                ->first();

            Auth::setUser($user);

            return $next($request);
        }catch(\Exception $e){
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
            return response()->json([
                'message' => 'Something went wrong',
            ]);
        }
    }
}
