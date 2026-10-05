<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorAuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Second step of admin login when the account has 2FA enabled. Deliberately
 * a plain controller (not a Filament page/Livewire component) — the login
 * flow it hands off from already does the password check and re-logs the
 * user in on success here, so this only needs ordinary session + auth
 * facade calls, which is easier to get right without a live environment to
 * test against than reaching into Filament's Livewire internals a second
 * time.
 *
 * Reached only via App\Filament\Auth\Login::authenticate(), which sets the
 * 'two_factor.user_id' session key after a correct password — visiting
 * this page directly without that key just bounces back to login.
 */
class TwoFactorChallengeController extends Controller
{
    public function __construct(private TwoFactorAuthenticationService $twoFactor)
    {
    }

    public function show()
    {
        if (! session()->has('two_factor.user_id')) {
            return redirect('/admin/login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $userId = session('two_factor.user_id');

        if (! $userId) {
            return redirect('/admin/login');
        }

        $user = User::find($userId);

        if (! $user || $user->role !== 'admin') {
            session()->forget(['two_factor.user_id', 'two_factor.remember']);

            return redirect('/admin/login');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = $request->input('code');
        $verified = $this->twoFactor->verifyCode((string) $user->two_factor_secret, $code);

        if (! $verified) {
            $verified = $this->consumeRecoveryCodeIfValid($user, $code);
        }

        if (! $verified) {
            throw ValidationException::withMessages([
                'code' => 'Kode tidak valid. Coba lagi.',
            ]);
        }

        $remember = (bool) session('two_factor.remember', false);
        session()->forget(['two_factor.user_id', 'two_factor.remember']);

        Auth::guard('web')->login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended('/admin');
    }

    /**
     * Recovery codes are single-use: a match removes it from the stored
     * list so it can't be replayed.
     */
    private function consumeRecoveryCodeIfValid(User $user, string $inputCode): bool
    {
        $recoveryCodes = $user->two_factor_recovery_codes ?? [];
        $normalizedInput = strtoupper(trim($inputCode));

        if (! in_array($normalizedInput, $recoveryCodes, true)) {
            return false;
        }

        $user->two_factor_recovery_codes = array_values(array_diff($recoveryCodes, [$normalizedInput]));
        $user->save();

        return true;
    }
}
