<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Neplatné prihlasovacie údaje'], 401);
        }

        $user = auth('api')->user();
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Odhlásený']);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
