<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Show the change password page for admin.
     */
    public function password(Request $request): View
    {
        return view('admin.profile.password', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'note' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->mobile = $validated['mobile'] ?? $user->mobile;
        $user->note = $validated['note'] ?? $user->note;

        // profile image upload
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return Redirect::route('admin.profile.edit')->withSuccess('profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        
        $loginId = Auth()->id();
        $userInput = [
            'password' => Hash::make($validatedData['password'])
        ];

        User::where('id',$loginId)->update($userInput);
        
        return Redirect::route('admin.profile.edit')->withSuccess('password-updated');
    }
}
