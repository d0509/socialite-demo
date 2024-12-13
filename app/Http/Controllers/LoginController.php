<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Facebook authentication failed.');
        }

        // Check if the user already exists
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            // Login the existing user
            Auth::login($existingUser);
        } else {
            // Create a new user
            $newUser = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->password = Hash::make($user->password);
            $newUser->save();

            Auth::login($newUser);
        }

        return redirect()->route('home');
    }
}
