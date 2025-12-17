<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        try {
            $user = User::create($data);

            return response()->json(
                [
                    'success' => true,
                    'data' => $user,
                    'token' => $user->createToken('MyApp')->plainTextToken,
                    'message' => 'User registered successfully',
                    'code' => 201
                ]
            );
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($data)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('MyApp')->plainTextToken;
        return response()->json(['success' => true, 'data' => $user, 'token' => $token]);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }
}
