<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function profile(){
        $data = Auth::user();

        return new UserResource($data);
    }
    public function profileUpdate(ProfileUpdateRequest $request){
        try{
            $user = Auth::user();
            $validate = $request->validated();

            $userData = Arr::only($validate, ['email', 'name']);
            $profileData = Arr::only($validate, ['phone', 'address']);
            $user->update($userData);

            // if profile image:

            if($request->hasFile('image')){
                $path  = $request->file('image')->store('avaters', 'public');
                $profileData['avatar'] = $path;
            }

            $user->profile()->update($profileData);

            return response([
                'status' => true,
                'message' => 'Profile Updated Successfully',
                'data' => new UserResource($user),
            ]);
        }catch (\Exception $e){
            Log::critical($e->getMessage() . ' ' .  $e->getFile() . ' ' . $e->getLine());
            return response([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
}
