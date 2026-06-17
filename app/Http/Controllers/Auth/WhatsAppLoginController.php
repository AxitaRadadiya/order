<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginOtp;
use App\Models\User;
use App\Services\WhatsAppOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WhatsAppLoginController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.login');
    }

    public function requestOtp(Request $request, WhatsAppOtpService $whatsAppOtpService): RedirectResponse
    {
        $validated = $request->validate([
            'mobile' => ['required', 'digits:10'],
        ]);

        // Keep strict: exactly 10 digits only (no + or spaces)
        $mobile = preg_replace('/\s+/', '', $validated['mobile']);
        if (!preg_match('/^\d{10}$/', $mobile)) {
            return back()->withErrors(['mobile' => 'Mobile number must be exactly 10 digits.']);
        }


        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return back()->withErrors(['mobile' => 'Mobile number not found.']);
        }

        // Create new OTP
        LoginOtp::where('mobile', $mobile)->delete();

        $otp = (string) random_int(100000, 999999);

        LoginOtp::create([
            'mobile' => $mobile,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via WhatsApp
        try {
            $whatsAppOtpService->sendOtp($mobile, $otp);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp OTP send failed in requestOtp', [
                'mobile' => $mobile,
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'mobile' => 'We could not send the OTP right now. Please try again in a moment.',
            ]);
        }

        // Keep mobile in session for verify step
        $request->session()->put('whatsapp_login_mobile', $mobile);

        // Reuse the same `mobile` name in verify form submit payload.
        $request->session()->put('whatsapp_login_mobile_verified_input', $mobile);


        // Stay on same login page and show OTP field.
        return redirect()->route('login')->with('status', 'OTP sent to your mobile number.');

    }

    public function showVerifyForm(): View
    {
        $mobile = session('whatsapp_login_mobile');

        return view('auth.verify-whatsapp-otp', [
            'mobile' => $mobile,
        ]);

    }



    public function verifyOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
            'mobile' => ['required', 'digits:10'],
        ]);

        $mobile = str_replace(' ', '', $validated['mobile']);
        $mobile = ltrim($mobile, '+');

        if (! $mobile) {

            return redirect()->route('login')->withErrors(['otp' => 'OTP session expired. Please request OTP again.']);
        }


        $otpRecord = LoginOtp::where('mobile', $mobile)
            ->where('otp', $validated['otp'])

            ->whereNull('verified_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (! $otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $otpRecord->update(['verified_at' => now()]);

        $user = User::where('mobile', $mobile)->first();
        if (! $user) {
            return redirect()->route('login')->withErrors(['mobile' => 'Mobile number not found.']);
        }

        Auth::guard('web')->login($user);

        // Mirror your existing AuthenticatedSessionController logic for session token + module access.
        $newToken = Str::random(60);
        $user->forceFill(['session_token' => $newToken])->save();
        $request->session()->put('session_token', $newToken);

        // Keep your module access logic aligned with AuthenticatedSessionController.
        try {
            $role = $user->role;

            if ($user->hasRole(['super-admin', 'admin'])) {
                $request->session()->forget('allowed_modules');
                return redirect()->intended(route('dashboard', absolute: false));
            } elseif ($role) {
                $permissionNames = $role->permissions()->pluck('name')->map(fn ($n) => strtolower($n))->toArray();

                if (in_array('all-modules', $permissionNames, true) || in_array('all', $permissionNames, true)) {
                    $request->session()->forget('allowed_modules');
                    return redirect()->intended(route('dashboard', absolute: false));
                }

                $modules = [];

                if ($user->hasRole('distributor')) {
                    $modules = ['catalog', 'orders', 'settings'];

                    $rolePerms = $role ? $role->permissions()->pluck('name')->map(fn ($n) => strtolower($n))->toArray() : [];
                    if (collect($rolePerms)->contains(fn ($p) => str_starts_with($p, 'customer-'))) {
                        $modules[] = 'customers';
                    }
                } elseif ($user->hasRole('retailer')) {
                    if (! empty($user->distributor_verified)) {
                        $modules = ['catalog', 'orders'];
                    } else {
                        $modules = [];
                    }

                    $rolePerms = $role ? $role->permissions()->pluck('name')->map(fn ($n) => strtolower($n))->toArray() : [];
                    if (collect($rolePerms)->contains(fn ($p) => str_starts_with($p, 'customer-'))) {
                        $modules[] = 'customers';
                    }
                } else {
                    foreach ($permissionNames as $perm) {
                        if (str_starts_with($perm, 'item-')) {
                            $modules[] = 'items';
                        }
                        if (str_starts_with($perm, 'order-')) {
                            $modules[] = 'orders';
                        }
                        if (str_starts_with($perm, 'catalog-') || $perm === 'catalog') {
                            $modules[] = 'catalog';
                        }
                        if (str_starts_with($perm, 'dashboard-')) {
                            $modules[] = 'dashboard';
                        }
                        if (str_starts_with($perm, 'user-')) {
                            $modules[] = 'users';
                        }
                        if (str_starts_with($perm, 'customer-')) {
                            $modules[] = 'customers';
                        }
                        if (str_starts_with($perm, 'setting-') || str_starts_with($perm, 'role-') || str_starts_with($perm, 'permission-')) {
                            $modules[] = 'settings';
                        }
                        if (str_starts_with($perm, 'report-')) {
                            $modules[] = 'reports';
                        }
                    }
                }

                $modules = array_values(array_unique(array_map(fn ($m) => strtolower($m), $modules)));

                if (empty($modules)) {
                    $request->session()->forget('allowed_modules');
                } else {
                    $request->session()->put('allowed_modules', $modules);
                }
            } else {
                $request->session()->forget('allowed_modules');
            }
        } catch (\Throwable $e) {
            $request->session()->forget('allowed_modules');
        }

        // Redirect logic similar to AuthenticatedSessionController
        try {
            if ($user->hasRole('distributor')) {
                return redirect()->intended(route('catalog', absolute: false));
            }
            if ($user->hasRole('retailer') && ! empty($user->distributor_verified)) {
                return redirect()->intended(route('catalog', absolute: false));
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }
}