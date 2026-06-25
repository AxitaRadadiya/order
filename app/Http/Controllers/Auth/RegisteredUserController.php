<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\State;
use App\Models\City;
use App\Models\LoginOtp;
use App\Models\Country;
use App\Services\WhatsAppOtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // $distributors = User::whereHas('role', function ($q) { $q->where('name', 'distributor'); })->get(['id','company_name','name']);
        $countries = Country::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('auth.register', compact('countries', 'states', 'cities'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request, WhatsAppOtpService $whatsAppOtpService): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'mobile' => ['required', 'digits:10'],
            'shop_name' => ['nullable', 'string', 'max:255'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'gst_number' => ['nullable', 'string', 'max:255'],
            // 'distributor_id' => ['nullable', 'exists:users,id'],
        ]);

        $role = Role::firstOrCreate(['name' => 'retailer']);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name'     => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'mobile' => $request->input('mobile'),
            'shop_name' => $request->input('shop_name'),
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'gst_number' => $request->input('gst_number'),
            // 'distributor_id' => $request->input('distributor_id'),
            'password' => Hash::make('12345678'),
            'role_id' => $role->id,
            // optionally mark distributor_verified so retailer gets catalog/orders access immediately
            'distributor_verified' => true,
        ]);

        // Create OTP and send via WhatsApp for verification before first login
        LoginOtp::where('mobile', $user->mobile)->delete();
        $otp = (string) random_int(100000, 999999);

        LoginOtp::create([
            'mobile' => $user->mobile,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            $whatsAppOtpService->sendOtp($user->mobile, $otp);
        } catch (\Throwable $e) {
            // Log and fallback: delete created user to avoid orphan
            \Log::error('Registration OTP send failed', ['mobile' => $user->mobile, 'error' => $e->getMessage()]);
            $user->delete();
            return back()->withErrors(['mobile' => 'Could not send OTP right now. Please try again later.']);
        }

        // Keep mobile in session so login flow can verify OTP
        $request->session()->put('whatsapp_login_mobile', $user->mobile);

        // Fire Registered event (email verification or other listeners may use it)
        event(new Registered($user));

        return redirect()->route('login')->with('status', 'OTP sent to your mobile number. Please verify to complete registration.');
    }
}
