<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileWebController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->password_force_change && !$request->filled('new_password')) {
            return back()->withErrors(['new_password' => 'You are required to change your password before continuing.'])->withInput();
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'confirmed'],
        ]);

        if ($request->filled('new_password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }

            // Enforce password policies
            $minLength = \App\Models\Setting::get('password_min_length', 8);
            $requireSpecial = \App\Models\Setting::get('password_require_special', false);
            $rules = ['required', 'string', 'min:' . $minLength, 'confirmed'];
            if ($requireSpecial) {
                $rules[] = 'regex:/[!@#$%^&*(),.?":{}|<>]/';
            }
            $request->validate(['new_password' => $rules], [
                'new_password.regex' => 'The password must contain at least one special character.',
            ]);

            $user->password = Hash::make($request->new_password);
            $user->password_force_change = false;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
