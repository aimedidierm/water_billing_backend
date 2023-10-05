<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response as HttpResponse;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if ($user != null) {
            $passwordMatch = Hash::check($password, $user->password);

            if ($passwordMatch) {

                Auth::login($user);
                $token = $user->createToken('token')->accessToken;
                return response()->json([
                    'user' => $user,
                    'token' => $token
                ], 200);
            } else {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } else {
            return response()->json(['error' => 'Invalid email'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "email" => "required|email|unique:users,email",
                "name" => "required",
                "password" => "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }


        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->role = 'client';
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'message' => 'account created',
        ], 200);
    }

    public function logout()
    {
        if (Auth::guard('api')->check()) {
            Auth::logout();
            return response()->json([
                'message' => 'User signed out',
            ], 200);
        } else {
            return response()->json(['error' => 'Invalid user session'], 401);
        }
    }
}
