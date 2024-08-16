<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function signup(Request $request)
    {
        $validateData = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8'
            ]);

        if($validateData->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateData->errors()->all(),
            ]);
        }

        $user = User::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ],201);
    }

    public function login(Request $request)
    {
        $validateData = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required|string|min:8'
            ]
        );

        if($validateData->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Authentication Fails',
                'errors' => $validateData->errors()->all(),
            ],404);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            return response()->json([
                'status' => true,
                'message' => 'User login successfully',
                'token' => $user->createToken("authToken")->plainTextToken,
                'token_type' => 'Bearer'
            ],200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Email or password is incorrect',
            ],401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'User logout successfully',
        ],200);
    }
}
