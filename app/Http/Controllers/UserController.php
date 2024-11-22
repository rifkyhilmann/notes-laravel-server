<?php

namespace App\Http\Controllers;

use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function Login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // cari users berdasarkan password
        $user = users::where('email', $request->email)->first();

        // Validasi password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->token = $token;
        $user->save();

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request) {
        $request->users()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    public function Regis(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
        ]);

        $user = new users();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password =Hash::make($validated['password']); // Hash password
        $user->address = $validated['address'];
        $user->phone = $validated['phone'];
        $user->save();

        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
        ], 201);
    }

    public function getAllUsers(Request $request) {
        // Mendapatkan semua data user dari tabel 'users'
        $users = users::all();
    
        // Mengembalikan data users dalam bentuk JSON
        return response()->json([
            'message' => 'List of all users',
            'users' => $users,
        ]);
    }
    

}
