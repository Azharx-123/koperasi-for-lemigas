<?php

namespace App\Filament\Pages;

use App\Services\TwoFactorAuthenticationService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Self-service 2FA setup/management for the logged-in admin. Deliberately
 * built on plain Livewire public properties (wire:model / wire:click) in
 * the view rather than Filament's Form Builder — fewer moving parts to get
 * exactly right without a live environment to test against, since this
 * page has several distinct states (no 2FA / pending confirmation /
 * enabled) rather than one single form.
 */
class TwoFactorSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Akun';

    protected static ?string $navigationLabel = 'Keamanan 2FA';

    protected static ?string $title = 'Autentikasi Dua Faktor';

    protected static string $view = 'filament.pages.two-factor-settings';

    public ?string $pendingSecret = null;

    public ?string $pendingQrUrl = null;

    public string $confirmCode = '';

    public string $disablePassword = '';

    public ?array $recoveryCodesToShow = null;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->two_factor_secret && ! $user->two_factor_confirmed_at) {
            $this->pendingSecret = $user->two_factor_secret;
            $this->pendingQrUrl = app(TwoFactorAuthenticationService::class)
                ->getQrCodeUrl(config('app.name', 'KEP'), $user->email, $user->two_factor_secret);
        }
    }

    public function generateSecret(): void
    {
        $service = app(TwoFactorAuthenticationService::class);
        $user = Auth::user();
        $secret = $service->generateSecretKey();

        $user->two_factor_secret = $secret;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        $this->pendingSecret = $secret;
        $this->pendingQrUrl = $service->getQrCodeUrl(config('app.name', 'KEP'), $user->email, $secret);
        $this->recoveryCodesToShow = null;
    }

    public function confirmTwoFactor(): void
    {
        $service = app(TwoFactorAuthenticationService::class);
        $user = Auth::user();

        if (! $user->two_factor_secret || ! $service->verifyCode($user->two_factor_secret, $this->confirmCode)) {
            Notification::make()->title('Kode tidak valid, coba lagi.')->danger()->send();
            $this->confirmCode = '';

            return;
        }

        $codes = $service->generateRecoveryCodes();

        $user->two_factor_confirmed_at = now();
        $user->two_factor_recovery_codes = $codes;
        $user->save();

        $this->pendingSecret = null;
        $this->pendingQrUrl = null;
        $this->confirmCode = '';
        $this->recoveryCodesToShow = $codes;

        Notification::make()->title('Autentikasi dua faktor berhasil diaktifkan')->success()->send();
    }

    public function cancelSetup(): void
    {
        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $this->pendingSecret = null;
        $this->pendingQrUrl = null;
        $this->confirmCode = '';
    }

    public function regenerateRecoveryCodes(): void
    {
        $user = Auth::user();

        if (! $user->hasTwoFactorEnabled()) {
            return;
        }

        $codes = app(TwoFactorAuthenticationService::class)->generateRecoveryCodes();
        $user->two_factor_recovery_codes = $codes;
        $user->save();

        $this->recoveryCodesToShow = $codes;

        Notification::make()->title('Kode pemulihan baru berhasil dibuat')->success()->send();
    }

    public function disableTwoFactor(): void
    {
        $user = Auth::user();

        if (! Hash::check($this->disablePassword, $user->password)) {
            Notification::make()->title('Password salah')->danger()->send();
            $this->disablePassword = '';

            return;
        }

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $this->disablePassword = '';
        $this->recoveryCodesToShow = null;

        Notification::make()->title('Autentikasi dua faktor dinonaktifkan')->success()->send();
    }
}
