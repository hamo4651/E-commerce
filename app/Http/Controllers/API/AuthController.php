<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
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

   /**
    * Register a new user
    *
    * @group Authentication
    * @bodyParam name string required The full name of the user. Example: John Doe
    * @bodyParam email string required The email address of the user. Example: johndoe@example.com
    * @bodyParam password string required The password for the user. Example: secret123
    * @bodyParam password_confirmation string required The password confirmation. Example: secret123
    *
    * @response 201 {
    *    "message": "User registered successfully",
    *    "user": { "id": 1, "name": "John Doe", "email": "johndoe@example.com" }
    * }
    */
   public function register(RegisterRequest $request)
   {
       $user = $this->data->register($request->validated());
       event(new Registered($user));

       return response()->json([
           'message' => 'User registered successfully',
           'user' => new UserResource($user), 
       ], 201);
   }

   /**
    * User login
    *
    * @group Authentication
    * @bodyParam email string required The email of the user. Example: johndoe@example.com
    * @bodyParam password string required The password of the user. Example: secret123
    *
    * @response 200 {
    *    "message": "Login successfully",
    *    "access_token": "token_here",
    *    "token_type": "Bearer",
    *    "user": { "id": 1, "name": "John Doe", "email": "johndoe@example.com" }
    * }
    */
   public function login(LoginRequest $request)
   {
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
           'user' => new UserResource($authData['user']), 
       ]);
   }

   /**
    * Get authenticated user profile
    *
    * @group Authentication
    * @authenticated
    *
    * @response 200 {
    *    "user": { "id": 1, "name": "John Doe", "email": "johndoe@example.com" }
    * }
    */
   public function profile()
   {
       $user = $this->data->profile();
       return response()->json([
           'user' => new UserResource($user), 
       ], 200);
   }

   /**
    * Logout user
    *
    * @group Authentication
    * @authenticated
    *
    * @response 200 {
    *    "success": true,
    *    "message": "Logged out successfully"
    * }
    */
   public function logout(Request $request)
   {
       return response()->json([
           'success' => $this->data->logout($request),
           'message' => 'Logged out successfully'
       ], 200);
   }
}
