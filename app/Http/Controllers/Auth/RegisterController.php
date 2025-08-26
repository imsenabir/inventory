<?php

namespace App\Http\Controllers\Auth;

use App\Models\Profile;
use Exception;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    public function Register(RegisterRequest $request)
    {

        $validate = $request->validated();

        $userData = Arr::only($validate, ['email', 'password', 'name', 'role']);
        $profileData = Arr::only($validate, ['phone', 'address']);

        try {
            // User::create($request->validated());
            $user = User::create($userData);

            $profileData['user_id'] = $user->id;

            // if profile image:

            if($request->hasFile('image')){
                $path  = $request->file('image')->store('avaters', 'public');
                $profileData['avatar'] = $path;
            }

            Profile::create($profileData);
            // $user->profile()->create($profileData);

            return response()->json([
                'status' => true,
                'message' => "User Created Successfully",
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getCode());
            return response()->json([
                'status' => false,
                // 'message' => $e->getMessage()
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
