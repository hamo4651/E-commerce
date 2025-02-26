<?php 
namespace App\Services;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    public function authenticateUser()
    {
        
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser || !isset($googleUser->id)) {
                throw new \Exception('Invalid Google user data');
            }

            $user = User::firstOrCreate(
                ['social_id' => $googleUser->id],
                [
                    'name'        => $googleUser->name ?? 'Unknown',
                    'email'       => $googleUser->email ?? null,
                    'image'       => $googleUser->avatar ?? null,
                    'is_admin'    => 0,
                    'social_type' => 'google',
                    'password'    => Hash::make('my-google'),
                ]
            );

            return [
                'user'  => $user,
                'token' => $user->createToken('ecomm')->plainTextToken,
            ];
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            throw new \Exception('Authentication failed. Please try again.');
        }
    }
}
