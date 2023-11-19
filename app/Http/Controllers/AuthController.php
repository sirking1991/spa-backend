<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password']
        ]);

        $token = $user->createToken('myapptoekn')->plainTextToken;

        return response()->json([
            'user'=>$user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !password_verify($validated['password'], $user->password)) {
            return response(status: 401);
        }

        $token = $user->createToken('myapptoekn')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }
}
