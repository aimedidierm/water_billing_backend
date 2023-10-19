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
                'email' => 'required|email|string',
                'password' => 'required|string'
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
                return response()->json(['error' => 'Ntago wemerewe'], 401);
            }
        } else {
            return response()->json(['error' => 'Email ntago yemewe'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "email" => "required|email|unique:users,email",
                "name" => "required",
                "phone" => "required|string",
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
        $user->phone = $request->phone;
        $user->role = 'client';
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json([
            'message' => 'Kwandikisha konti yanyu byakunze',
        ], 200);
    }

    public function logout()
    {
        if (Auth::guard('api')->check()) {
            Auth::logout();
            return response()->json([
                'message' => 'Wasohotse',
            ], 200);
        } else {
            return response()->json(['error' => 'Kubikora ntubyemerewe'], 401);
        }
    }
}
