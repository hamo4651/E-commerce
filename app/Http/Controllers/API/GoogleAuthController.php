<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $code = $request->get('code');
    
            if (!$code) {
                return response()->json(['error' => 'Authorization code is missing'], 400);
            }
    
            // طلب تبديل الكود إلى access_token و id_token
            $client = new Client();
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id'     => env('GOOGLE_CLIENT_ID'),
                    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                    'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                ],
            ]);
    
            $tokenData = json_decode($response->getBody()->getContents());
    
            if (!isset($tokenData->id_token)) {
                return response()->json(['error' => 'Failed to get ID token'], 400);
            }
    
            // الآن لدينا id_token، نستخدمه لجلب بيانات المستخدم
            $googleUserResponse = $client->get("https://www.googleapis.com/oauth2/v3/tokeninfo?id_token={$tokenData->id_token}");
            $googleUser = json_decode($googleUserResponse->getBody()->getContents());
    
            if (!$googleUser || !isset($googleUser->sub)) {
                return response()->json(['error' => 'Invalid Google user'], 401);
            }
    
            // البحث عن المستخدم أو إنشاؤه
            $user = User::firstOrCreate([
                'social_id'   => $googleUser->sub,
            ], [
                'name'        => $googleUser->name ?? 'Unknown',
                'email'       => $googleUser->email ?? null,
                'image'       => $googleUser->picture ?? null,
                'role'        => 'user',
                'social_type' => 'google',
                'password'    => Hash::make('my-google'),
            ]);
    
            return response()->json([
                'user'  => $user,
                'token' => $user->createToken('ecomm')->plainTextToken,
            ]);
    
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    
}