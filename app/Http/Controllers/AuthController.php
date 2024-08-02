<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            if ($validateUser->fails()) {
                return Response()->json([
                    'typeError' => 'validation',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            if(!Auth::attempt($request->only(['email', 'password']))) {
                return Response()->json([
                    'typeError' => 'notFound',
                    'error' => 'Usuario y/o Contraseña Incorrectos'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'typeError' => 'unexpected',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
