<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Block deletion while the user has orders still in progress —
        // these are business records tied to a live fulfillment, not
        // just personal data that's safe to wipe.
        $hasActiveOrders = Order::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'shipped'])
            ->exists();

        if ($hasActiveOrders) {
            return Redirect::route('profile.edit')->withErrors([
                'userDeletion' => 'Anda tidak dapat menghapus akun selagi memiliki pesanan yang masih diproses atau dikirim. Selesaikan atau batalkan pesanan tersebut terlebih dahulu.',
            ], 'userDeletion');
        }

        // Logout BEFORE deleting the row, not after: Auth::logout() cycles
        // the user's remember-token, which internally calls $user->save().
        // If that runs after $user->delete() has already set $user->exists
        // to false, Eloquent treats save() as a fresh insert rather than
        // an update — silently resurrecting the row this method just
        // deleted, with a new remember_token but otherwise identical data
        // (this used to be exactly what happened here). Logging out first
        // means that save() still lands on a row that still exists, so
        // it's a harmless update to a row that's about to be deleted anyway.
        Auth::logout();

        DB::transaction(function () use ($user) {
            // Cascade-delete data that's only meaningful while the account exists
            if ($user->cart) {
                $user->cart->products()->detach();
                $user->cart->delete();
            }

            // Anonymize rather than hard-delete: a rating's score/review
            // still matters to a product's average and to other buyers
            // reading it, even once the reviewer's account is gone. Only
            // the link back to the (now-deleted) user is removed.
            Rating::where('user_id', $user->id)->update(['user_id' => null]);

            $user->delete();
        });

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
