<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('settings.index', compact('user'));
    }

    public function updateTheme(Request $request)
    {
        $data = $request->validate([
            'theme' => ['required', 'string', 'in:light,dark'],
        ]);

        $user = Auth::user();
        $user->update(['theme_preference' => $data['theme']]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Theme updated successfully.']);
        }

        return back()->with('success', 'Tema berhasil diperbarui.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? $user->phone,
        ];

        if ($request->filled('current_password')) {
            if (!Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }

            $updateData['password'] = Hash::make($data['new_password']);
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($updateData);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Profile updated successfully.']);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
