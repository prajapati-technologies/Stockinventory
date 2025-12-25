<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users,phone_number,' . $user->id,
            'district_id' => 'nullable|exists:districts,id',
            'mandal_id' => 'nullable|exists:mandals,id',
        ]);

        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'district_id' => $request->district_id,
            'mandal_id' => $request->mandal_id,
        ]);

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('profile.index')->with('success', 'Password changed successfully.');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'language' => 'in:en,hi',
            'timezone' => 'string|max:50',
        ]);

        // Store settings in user preferences (you can create a user_preferences table or use JSON column)
        $settings = [
            'email_notifications' => $request->boolean('email_notifications', true),
            'sms_notifications' => $request->boolean('sms_notifications', false),
            'language' => $request->input('language', 'en'),
            'timezone' => $request->input('timezone', 'Asia/Kolkata'),
        ];

        $user->update([
            'preferences' => json_encode($settings),
        ]);

        return redirect()->route('profile.settings')->with('success', 'Settings updated successfully.');
    }
}