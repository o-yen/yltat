<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Mail\OtpMail;
use App\Models\MobileAccessToken;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends BaseMobileController
{
    public function login(Request $request)
    {
        $throttleKey = Str::lower((string) $request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return $this->error('Too many login attempts. Please try again later.', 429, [
                'retry_after' => [RateLimiter::availableIn($throttleKey)],
            ]);
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            RateLimiter::hit($throttleKey, 60);
            return $this->error(__('auth.failed'), 422);
        }

        RateLimiter::clear($throttleKey);

        $user = Auth::user()->load('role');

        if ($user->status !== 'active') {
            Auth::logout();
            return $this->error(__('auth.account_inactive'), 403);
        }

        [$token, $plainTextToken] = MobileAccessToken::issue(
            $user,
            $validated['device_name'] ?? 'mobile',
            ['*'],
            now()->addDays(30)
        );

        return $this->success([
            'token' => $plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => optional($token->expires_at)->toIso8601String(),
            'user' => $this->userPayload($user),
        ], 'Logged in successfully.');
    }

    public function me()
    {
        return $this->success([
            'user' => $this->userPayload(auth()->user()->load('role')),
        ]);
    }

    public function logout(Request $request)
    {
        $token = $this->currentToken($request);
        if ($token) {
            $token->delete();
        }

        return $this->success([], 'Logged out successfully.');
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return $this->error(__('auth.password_incorrect'), 422, [
                'current_password' => [__('auth.password_incorrect')],
            ]);
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        return $this->success([], __('messages.password_changed'));
    }

    public function forgotPasswordSendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->where('status', 'active')->first();

        if (!$user) {
            return $this->error(__('auth.email_not_found'), 404);
        }

        $otp = PasswordResetOtp::generateFor($user->id);

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp));
        } catch (\Throwable $e) {
            return $this->error(__('messages.otp_send_failed'), 500);
        }

        return $this->success([
            'email' => $user->email,
        ], __('messages.otp_sent'));
    }

    public function forgotPasswordVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->where('status', 'active')->first();
        if (!$user) {
            return $this->error(__('auth.email_not_found'), 404);
        }

        $record = PasswordResetOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$record || !$record->isValid()) {
            return $this->error(__('messages.otp_invalid'), 422);
        }

        // Return a reset token for the next step
        $resetToken = Str::random(64);
        $record->update(['reset_token' => $resetToken]);

        return $this->success([
            'reset_token' => $resetToken,
        ], __('messages.otp_verified'));
    }

    public function forgotPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reset_token' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->where('status', 'active')->first();
        if (!$user) {
            return $this->error(__('auth.email_not_found'), 404);
        }

        $record = PasswordResetOtp::where('user_id', $user->id)
            ->where('reset_token', $request->reset_token)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$record || !$record->isValid()) {
            return $this->error(__('messages.otp_invalid'), 422);
        }

        $record->update(['used' => true]);
        $user->update(['password' => Hash::make($request->new_password)]);

        return $this->success([], __('messages.password_changed'));
    }

    public function updateLanguage(Request $request)
    {
        $validated = $request->validate([
            'language' => 'required|in:ms,en',
        ]);

        $user = auth()->user();
        $user->update(['language' => $validated['language']]);

        return $this->success([
            'user' => $this->userPayload($user->fresh('role')),
        ], 'Language updated successfully.');
    }
}
