<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PasswordResetOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProfileController extends BaseTalentController
{
    public function show()
    {
        $talent = $this->getTalent();

        return view('talent.profile.show', compact('talent'));
    }

    public function edit()
    {
        $talent = $this->getTalent();

        return view('talent.profile.edit', compact('talent'));
    }

    public function update(Request $request)
    {
        $talent = $this->getTalent();

        $request->validate([
            'phone'           => 'required|string|max:20',
            'address'         => 'nullable|string|max:500',
            'skills_text'     => 'nullable|string|max:1000',
            'profile_summary' => 'nullable|string|max:1000',
        ]);

        $talent->update($request->only('phone', 'address', 'skills_text', 'profile_summary'));

        return redirect()->route('talent.profile.show')
            ->with('success', __('messages.profile_updated'));
    }

    public function updatePhoto(Request $request)
    {
        $talent = $this->getTalent();

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($talent->profile_photo) {
            Storage::disk('public')->delete($talent->profile_photo);
        }

        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $talent->update(['profile_photo' => $path]);

        return redirect()->route('talent.profile.show')
            ->with('success', __('messages.profile_photo_updated'));
    }

    // ─── Change password ─────────────────────────────────────────────────────────

    public function changePasswordForm()
    {
        return view('talent.profile.change-password', [
            'user'     => auth()->user(),
            'otpSent'  => session('otp_sent', false),
            'otpValid' => session('otp_valid', false),
        ]);
    }

    public function requestOtp()
    {
        $user = auth()->user();
        $otp  = PasswordResetOtp::generateFor($user->id);

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.otp_send_failed'));
        }

        return redirect()->route('talent.profile.change-password')
            ->with('otp_sent', true)
            ->with('success', __('messages.otp_sent'));
    }

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

        session(['otp_verified_id' => $record->id]);

        return redirect()->route('talent.profile.change-password')
            ->with('otp_valid', true)
            ->with('success', __('messages.otp_verified'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $otpId = session('otp_verified_id');

        if (! $otpId) {
            return redirect()->route('talent.profile.change-password')
                ->with('error', __('messages.otp_invalid'));
        }

        $record = PasswordResetOtp::find($otpId);

        if (! $record || ! $record->isValid()) {
            session()->forget('otp_verified_id');
            return redirect()->route('talent.profile.change-password')
                ->with('error', __('messages.otp_invalid'));
        }

        $record->update(['used' => true]);
        session()->forget('otp_verified_id');

        auth()->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', __('messages.password_changed'));
    }
}
