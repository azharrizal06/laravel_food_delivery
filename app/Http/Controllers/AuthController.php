<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
        ]);
        //cek users
        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already exists in the database',
            ], 422); // 422 Unprocessable Entity
        }
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'user';

        $user = User::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ]);
    }
    //login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login success',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logout success'
        ]);
    }
    //register restaurant
    public function registerrestaurant(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'restaurant_name' => 'required|string',
            'restaurant_address' => 'required|string',
            'photo' => 'required|image',
            'latlong' => 'required|string',
        ]);
        //cek users
        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already exists in the database',
            ], 422);
        }
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'restaurant';

        $user = User::create($data);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $data['photo'] = $filename;
            $user->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ]);
    }
    //register driver
    public function registerDriver(Request $request)
    {
        $request->validate([

            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'license_plate' => 'required|string',
            'photo' => 'required|image',
        ]);
        //cek users
        $emailExists = User::where('email', $request->email)->exists();

        if ($emailExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already exists in the database',
            ], 422);
        }
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['roles'] = 'driver';

        $user = User::create($data);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $filename);
            $user->photo = $filename;
            $user->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ]);
    }
    //update latlong user
    public function updateLatLong(Request $request)
    {
        $request->validate([

            'latlong' => 'required|string',
        ]);

        $user = $request->user();
        $user->latlong = $request->latlong;
        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
    //get all restaurant
    public function getAllRestaurant()
    {
        $user = User::where('roles', 'restaurant')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
}
