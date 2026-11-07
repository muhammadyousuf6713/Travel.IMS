<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
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
            $inquiriesCount = \App\inquiry::where('saleperson', $user->id)->count();
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
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:3072'],
        ]);

        $updateData = [
            'name' => $attributes['name'],
            'email' => $attributes['email'],
        ];

        foreach (['phone', 'location', 'about_me'] as $col) {
            if (Schema::hasColumn('users', $col)) {
                $updateData[$col] = $attributes[$col] ?? null;
            }
        }

        // Handle profile picture upload or removal
        if ($request->hasFile('profile_picture')) {
            $dir = public_path('uploads/avatars');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Remove old picture if exists
            if (!empty($user->profile_picture) && File::exists(public_path($user->profile_picture))) {
                File::delete(public_path($user->profile_picture));
            }

            $file = $request->file('profile_picture');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $updateData['profile_picture'] = 'uploads/avatars/' . $fileName;
        } elseif ($request->boolean('remove_picture')) {
            if (!empty($user->profile_picture) && File::exists(public_path($user->profile_picture))) {
                File::delete(public_path($user->profile_picture));
            }
            $updateData['profile_picture'] = null;
        }

        $user->update($updateData);

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
