<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpPasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        PasswordResetOtp::where('email', $validated['email'])->delete();

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::create([
            'email' => $validated['email'],
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw("Your password reset OTP is {$otp}. It is valid for 10 minutes.", function ($message) use ($validated) {
            $message->to($validated['email'])->subject('Password Reset OTP');
        });

        return redirect()->route('password.verify')->with('email', $validated['email'])->with('status', 'OTP sent to your email address.');
    }

    public function verifyForm(): View
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $otpRecord = PasswordResetOtp::where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $otpRecord || $otpRecord->expires_at->isPast()) {
            return back()->withInput($request->only('email'))->withErrors([
                'otp' => 'Invalid or expired OTP.',
            ]);
        }

        $otpRecord->update(['verified_at' => now()]);

        $request->session()->put('password_reset_email', $validated['email']);

        return redirect()->route('password.reset')->with('status', 'OTP verified successfully.');
    }

    public function resetForm(Request $request): View
    {
        abort_unless($request->session()->has('password_reset_email'), 403);

        return view('auth.reset-password', ['token' => 'otp-verified']);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        abort_unless($email, 403);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        PasswordResetOtp::where('email', $email)->delete();
        $request->session()->forget('password_reset_email');

        return redirect()->route('login')->with('status', 'Password reset successful. Please log in.');
    }
}
