<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
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
        $distributors = User::whereHas('role', function ($q) { $q->where('name', 'distributor'); })->get(['id','company_name','name']);

        return view('auth.register', compact('distributors'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'mobile' => ['nullable', 'string', 'max:30'],
            'distributor_id' => ['nullable', 'exists:users,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = Role::firstOrCreate(['name' => 'retailer']);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->input('mobile'),
            'distributor_id' => $request->input('distributor_id'),
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
