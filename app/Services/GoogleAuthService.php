<?php namespace App\Services;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
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

            // البحث عن المستخدم في قاعدة البيانات
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // إذا كان الحساب موجودًا ولكن لم يتم ربطه بـ Google OAuth
                if (!$user->social_id) {
                    $user->update([
                        'social_id' => $googleUser->id,
                        'social_type' => 'google',
                    ]);
                }
            } else {
                // إنشاء مستخدم جديد إذا لم يكن موجودًا
                $user = User::create([
                    'name'        => $googleUser->name,
                    'email'       => $googleUser->email,
                    'social_id'   => $googleUser->id,
                    'social_type' => 'google',
                    'password'    => null, // عدم تعيين كلمة مرور
                ]);
            }
         
            // تسجيل دخول المستخدم
            // Auth::login($user);
            // dd($user);
            // إنشاء توكن للمستخدم
            $token = $user->createToken('ecomm')->plainTextToken;

            return response()->json([
                'message' => 'Authenticated successfully.',
                'user'    => $user,
                'token'   => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Authentication failed. Please try again.'
            ], 401);
        }
    }
}
