<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\JwtToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function Login(LoginRequest $request)
    {
        try {
            $user = User::whereEmail($request->email)->first();
            if (! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Credentials',
                ]);
            }

            $userData = [
                'name'  => $user->name,
                'email' => $user->email,
                'id'    => $user->id,
                'role'  => $user->role,
                'image' => $user->profile->avatar_url,
            ];
            $exp   = time() + 3600 * 24;
            $token = JwtToken::createToken($userData, time() + $exp);

            // $info = User::with('profile')->where('id', $user->id)->first();

            // $image = $info->profile->avatar;

            return response()->json([
                'status'    => true,
                'message'   => 'Login Success',
                'user_data' => new UserResource($user),

            ], 200)->cookie('token', $token['token'], $exp);

        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getCode());
            return response()->json([
                'error'   => false,
                'message' => $e->getMessage(),
                // 'message' => 'Something went wrong'
            ], 500);
        }
    }
}
