<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class AuthService
{

  public function register(array $data): User
  {
      $imagePath = null;
  
      if (isset($data['image'])) {
          $image = $data['image'];
          $imagePath = $image->store('users', 'public'); 
      }
  
      return User::create([
          'name' => $data['name'],
          'email' => $data['email'],
          'is_admin' => isset($data['is_admin']) && $data['is_admin'] ? 1 : 0, 
          'image' => $imagePath ? asset('storage/' . $imagePath) : null, 
          'password' => Hash::make($data['password']),
      ]);
  }
  
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return null;  
        }

        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }

    public function profile()
    {
      $user = Auth::user();

    if (!$user) {
        abort(401, 'Unauthorized');
    }

    return $user;
    }
    public function logout(Request $request): bool
    {
        $request->user()->currentAccessToken()->delete();  
        return true;
    }
}
