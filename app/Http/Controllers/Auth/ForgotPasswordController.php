<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password', [
            'step' => session('fp_step', 1),
            'email' => session('fp_email', ''),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->where('status', 'active')->first();

        if (!$user) {
            return back()->withInput()->with('error', __('auth.email_not_found'));
        }

        $otp = PasswordResetOtp::generateFor($user->id);

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp->otp));
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', __('messages.otp_send_failed'));
        }

        session(['fp_step' => 2, 'fp_email' => $user->email, 'fp_user_id' => $user->id]);

        return redirect()->route('password.forgot')
            ->with('success', __('messages.otp_sent'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $userId = session('fp_user_id');
        if (!$userId) {
            return redirect()->route('password.forgot')->with('error', __('auth.session_expired'));
        }

        $record = PasswordResetOtp::where('user_id', $userId)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$record || !$record->isValid()) {
            return back()
                ->with('fp_step', 2)
                ->with('fp_email', session('fp_email'))
                ->with('error', __('messages.otp_invalid'));
        }

        session(['fp_step' => 3, 'fp_otp_id' => $record->id]);

        return redirect()->route('password.forgot')
            ->with('success', __('messages.otp_verified'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $otpId = session('fp_otp_id');
        $userId = session('fp_user_id');

        if (!$otpId || !$userId) {
            return redirect()->route('password.forgot')->with('error', __('auth.session_expired'));
        }

        $record = PasswordResetOtp::find($otpId);
        if (!$record || !$record->isValid()) {
            session()->forget(['fp_step', 'fp_email', 'fp_user_id', 'fp_otp_id']);
            return redirect()->route('password.forgot')->with('error', __('messages.otp_invalid'));
        }

        $record->update(['used' => true]);

        $user = User::find($userId);
        $user->update(['password' => Hash::make($request->new_password)]);

        session()->forget(['fp_step', 'fp_email', 'fp_user_id', 'fp_otp_id']);

        return redirect()->route('login')->with('success', __('messages.password_changed'));
    }
}
