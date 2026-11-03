<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class InfoUserController extends Controller
{
    /**
     * Display the authenticated user's profile overview.
     */
    public function view()
    {
        $user = Auth::user();
        $inquiriesCount = 0;
        try {
            $inquiriesCount = \App\inquiry::where('sale_person_id', $user->id)->count();
        } catch (\Throwable $e) {
            $inquiriesCount = 0;
        }

        return view('profile.view', compact('user', 'inquiriesCount'));
    }

    /**
     * Show the edit profile form.
     */
    public function create()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile details.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'about_me' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'phone' => $attributes['phone'] ?? null,
            'location' => $attributes['location'] ?? null,
            'about_me' => $attributes['about_me'] ?? null,
        ]);

        return redirect()->route('profile.view')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the password update form.
     */
    public function passwordForm()
    {
        $user = Auth::user();
        return view('profile.password', compact('user'));
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password you entered is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.view')->with('success', 'Password updated successfully.');
    }
}
