<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => __('auth.account_inactive'),
                ]);
            }

            // Set user's preferred language
            session(['locale' => $user->language ?? 'ms']);

            return $this->redirectBasedOnRole($user);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    protected function redirectBasedOnRole($user)
    {
        $role = $user->role?->role_name ?? 'public';

        return match($role) {
            'super_admin', 'pmo_admin', 'mindef_viewer',
            'syarikat_pelaksana', 'rakan_kolaborasi' => redirect()->route('admin.dashboard'),
            'talent' => redirect()->route('talent.dashboard'),
            default => redirect()->route('portal.index'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('auth.logged_out'));
    }

    public function setLanguage(Request $request)
    {
        $lang = $request->input('lang', 'ms');
        if (in_array($lang, ['ms', 'en'])) {
            session(['locale' => $lang]);

            if (Auth::check()) {
                Auth::user()->update(['language' => $lang]);
            }
        }

        return back();
    }
}
