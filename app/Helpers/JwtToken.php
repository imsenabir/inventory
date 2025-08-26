<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class JwtToken
{
    /**
     * Create a new class instance.
     */

    // jwt token create
    public static function createToken(array $userData, int $exp):array{
        try {
            $key = config('jwt.jwt_key');
            $payload = $userData + [
                'iss' => 'PosInventoryApp',
                'iat' => time(),
                'exp' => $exp
            ];

            $token = JWT::encode($payload, $key, 'HS256');
            return [
                'error' => false,
                'token' => $token,
            ];

        // $key = env('JWT_KEY');
        } catch (Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' '. $e->getLine() . ' ' . $e->getCode() );
            return [
                'error' => true,
                'message' => 'Token creation failed',
            ];

        }
    }

    //jwt token verify
    public static function verifyToken(string $token):array{
        try {
            $key = config('jwt.jwt_key');
            if(!$token){
                return [
                    'error' => true,
                    'payload' => [],
                    'message' => 'Token not found',
                ];
            }

            $payload = JWT::decode($token, new Key(($key), 'HS256'));
            return [
                'error' => false,
                'payload' => $payload,
                'message' => 'Data found'
            ];
        } catch (Exception $e) {
            Log::critical($e->getMessage() . ' ' . $e->getFile() . ' '. $e->getLine() . ' ' . $e->getCode() );
            return [
                'error' => true,
                'payload' => [],
                'message' => 'Token verification failed',
            ];
        }
    }
}
