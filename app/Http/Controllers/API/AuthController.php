<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class AuthController extends Controller
{
   protected $data;
    public function __construct(AuthService $data)
    {
        $this->data = $data;
     }

    public function register(RegisterRequest $request)
    {
        $user = $this->data->register($request->validated());

        event(new Registered($user));

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
        
    }

    public function login(LoginRequest $request){
        
        $credentials = $request->validated();
        $authData = $this->data->login($credentials);
        if (!$authData) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successfully',
            'access_token' => $authData['token'],
            'token_type' => 'Bearer',
            'user' => $authData['user'],
        ]);
    }

    public function profile(){
        $user = $this->data->profile();
        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'success' => $this->data->logout($request),
            'message' => 'Logged out successfully'
        ], 200);
    }
    
}

