<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\JwtToken;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\ResetPasswordVerifyOtpRequest;
use App\Models\Otp;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Auth\ResetPasswordSendOtpRequest;

class ResetPasswordController extends Controller
{
    public function SendOtp(ResetPasswordSendOtpRequest $request)
    {
        try {
            $otp = mt_rand(100000, 999999);

            Otp::create([
                'email' => $request->email,
                'otp' => $otp,
            ]);

            Mail::to($request->email)->send(new SendOtpMail($otp));

            return response()->json([
                'status' => true,
                'message' => 'Otp send to your email',
                'otp' => $otp,
            ], 200);
        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getCode());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }// end method for send otp

    public function VerifyOtp(ResetPasswordVerifyOtpRequest $request)
    {
        try {
            Otp::where('email', $request->email)->where('otp', $request->otp)->update([
                'status' => true,
            ]);

            $exp = time() + 3600;
            $token = JwtToken::createToken(['email' => $request->email], $exp);
            return response()->json([
                'status' => true,
                'message' => 'Otp Verified',
            ], 200)->cookie('resetPasswordToken', $token['token'], $exp);

        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getCode());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }// end method for verify otp

    public function PasswordReset(PasswordResetRequest $request)
    {
        try {
            if (!$request->cookie('resetPasswordToken')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password request attempt',
                ], 200);
            }

            $decode = JwtToken::verifyToken($request->cookie('resetPasswordToken'));
            if ($decode['error']) {
                return response()->json([
                    'success' => false,
                    'message' => $decode['message'],
                ], 500);
            }

            $user = User::whereEmail($request['payload']->email)->first();
            $user->password = $request->password;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password han been reset',
            ], 200)->withoutCookie('resetPasswordToken');

        } catch (\Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getCode());
            return response()->json([
                'status' => false,
                // 'message' => $e->getMessage(),
                'message' => 'Password does not reset',
            ], 500);
        }
    }// end method for reset password
}
