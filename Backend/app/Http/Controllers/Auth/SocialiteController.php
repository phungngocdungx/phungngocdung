<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $socialiteUser = Socialite::driver('google')->user();

            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // User already exists, log them in
                Auth::login($user);

                // Check user roles after successful login
                // Only allow 'admin' (role_id 1) and 'manage' (role_id 2) roles
                if ($user->hasRole('admin') || $user->hasRole('manage')) {
                    return redirect()->route('home')->with('success', 'Đăng nhập bằng Google thành công!');
                } elseif ($user->hasRole('manage')) {
                    return redirect()->route(in_array($user->id, [2, 3]) ? 'show' : ($user->id === 5 ? 'home' : 'home'))->with('success', 'Đăng nhập bằng Google thành công!');
                } else {
                    // If the user has 'user' role (role_id 3) or any other unauthorized role, log them out
                    return redirect()->route('tiktok.index')->with('success', 'Đăng nhập bằng Google thành công!');
                }

            } else {
                // User doesn't exist, prevent new registration and show an error message
                return redirect()->route('login')->with('error', 'Hiện không hỗ trợ thêm mới tài khoản. Vui lòng liên hệ quản trị viên.');
            }
        } catch (\Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.');
        }
    }

    /**
     * Redirect the user to the Facebook authentication page.
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     */
    public function handleFacebookCallback()
    {
        try {
            $socialiteUser = Socialite::driver('facebook')->user();

            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // User already exists, log them in
                Auth::login($user);

                // Check user roles after successful login
                // Only allow 'admin' (role_id 1) and 'manage' (role_id 2) roles
                if ($user->hasRole('admin') || $user->hasRole('manage')) {
                    return redirect()->route('home')->with('success', 'Đăng nhập bằng Google thành công!');
                } elseif ($user->hasRole('manage')) {
                    return redirect()->route(in_array($user->id, [2, 3]) ? 'show' : ($user->id === 5 ? 'home' : 'home'))->with('success', 'Đăng nhập bằng Google thành công!');
                } else {
                    // If the user has 'user' role (role_id 3) or any other unauthorized role, log them out
                    return redirect()->route('tiktok.index')->with('success', 'Đăng nhập bằng Google thành công!');
                }
                
            } else {
                // User doesn't exist, prevent new registration and show an error message
                return redirect()->route('login')->with('error', 'Tài khoản của bạn chưa được đăng ký. Vui lòng liên hệ quản trị viên.');
            }
        } catch (\Exception $e) {
            Log::error('Facebook login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Facebook thất bại. Vui lòng thử lại.');
        }
    }
}
