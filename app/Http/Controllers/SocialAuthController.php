<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::where('provider_id', $socialUser->id)->first();

        if($user) {
            Auth::login($user);

            return redirect('/dashboard');
        }

        $user = User::where('email', $socialUser->email ?? null)->first();

        if($user) {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->id,
            ]);

            Auth::login($user);

            return redirect('/dashboard');
        }

        $user = User::create([
            'name' => $socialUser->name ?? $socialUser->nickname,
            'email' => $socialUser->email ?? null,
            'provider' => $provider,
            'provider_id' => $socialUser->id,
            'role' => 'user',
            'password' => bcrypt(Str::random(16)),
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }
}
