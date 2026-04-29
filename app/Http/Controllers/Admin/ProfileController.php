<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PasswordResetOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // ─── Show profile ───────────────────────────────────────────────────────────

    public function show()
    {
        return view('admin.profile.show', [
            'user' => auth()->user(),
        ]);
    }

    // ─── Edit profile form ───────────────────────────────────────────────────────

    public function edit()
    {
        return view('admin.profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    // ─── Update profile ──────────────────────────────────────────────────────────

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:30',
            'language'  => 'nullable|in:en,ms',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'language'  => $request->input('language', $user->language),
        ];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store("avatars/{$user->id}", 'public');
        }

        if ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->route('admin.profile.show')
            ->with('success', __('messages.profile_updated'));
    }

    // ─── Change password page ────────────────────────────────────────────────────

    public function changePasswordForm()
    {
        return view('admin.profile.change-password', [
            'user'     => auth()->user(),
            'otpSent'  => session('otp_sent', false),
            'otpValid' => session('otp_valid', false),
        ]);
    }

    // ─── Request / send OTP ──────────────────────────────────────────────────────

    public function requestOtp()
    {
        $user = auth()->user();
        $otp  = PasswordResetOtp::generateFor($user->id);

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.otp_send_failed'));
        }

        return redirect()->route('admin.profile.change-password')
            ->with('otp_sent', true)
            ->with('success', __('messages.otp_sent'));
    }

    // ─── Verify OTP ──────────────────────────────────────────────────────────────

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $user = auth()->user();
        $record = PasswordResetOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (! $record || ! $record->isValid()) {
            return back()
                ->with('otp_sent', true)
                ->with('error', __('messages.otp_invalid'));
        }

        // Mark as used only after password is actually changed — store ID in session
        session(['otp_verified_id' => $record->id]);

        return redirect()->route('admin.profile.change-password')
            ->with('otp_valid', true)
            ->with('success', __('messages.otp_verified'));
    }

    // ─── Update password ─────────────────────────────────────────────────────────

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $otpId = session('otp_verified_id');

        if (! $otpId) {
            return redirect()->route('admin.profile.change-password')
                ->with('error', __('messages.otp_invalid'));
        }

        $record = PasswordResetOtp::find($otpId);

        if (! $record || ! $record->isValid()) {
            session()->forget('otp_verified_id');
            return redirect()->route('admin.profile.change-password')
                ->with('error', __('messages.otp_invalid'));
        }

        // Invalidate the OTP
        $record->update(['used' => true]);
        session()->forget('otp_verified_id');

        // Update password
        auth()->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log out and redirect to login
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', __('messages.password_changed'));
    }
}
