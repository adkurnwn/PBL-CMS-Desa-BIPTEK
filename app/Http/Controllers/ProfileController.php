<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $userData = $request->validated();

        // Handle profile image upload
        if ($request->hasFile('photo_profile')) {
            // Delete the old photo if it exists
            if ($user->photo_profile) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $userData['photo_profile'] = $request->file('photo_profile')->store('profile-photos', 'public');
        }

        $user->fill($userData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully');
    }

    /**
     * Update profile image only.
     */
    public function updateImage(Request $request): RedirectResponse
    {
        $request->validate([
            'photo_profile' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete the old photo if it exists
        if ($user->photo_profile) {
            Storage::disk('public')->delete($user->photo_profile);
        }

        // Store the new photo
        $path = $request->file('photo_profile')->store('profile-photos', 'public');
        
        $user->update(['photo_profile' => $path]);

        return Redirect::route('profile.edit')->with('success', 'Profile image updated successfully');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
