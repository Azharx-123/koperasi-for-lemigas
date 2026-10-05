<?php

namespace App\Filament\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(),
                Checkbox::make('remember')
                    ->label('Ingat Saya'),
            ]);
    }

    /**
     * Overrides the base authenticate() method to match Filament's actual
     * contract for the login form's submit action (rate limiting, session
     * regeneration, LoginResponse), with an added role check on top so a
     * non-admin sees a clear "no access to admin panel" message instead of
     * a generic credentials-mismatch error.
     *
     * Note this role check is a UX nicety, not the actual security
     * boundary — User::canAccessPanel() independently blocks non-admins
     * from the panel regardless of this method.
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if ($user instanceof User && $user->role !== 'admin') {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'Akun ini tidak memiliki akses ke panel admin.',
            ]);
        }

        if ($user instanceof User && $user->hasTwoFactorEnabled()) {
            // Password check passed, but the panel login isn't complete
            // yet — log back out and hand off to the 2FA challenge route,
            // which re-establishes the session itself once the code (or a
            // recovery code) checks out. See TwoFactorChallengeController.
            Filament::auth()->logout();

            session([
                'two_factor.user_id' => $user->id,
                'two_factor.remember' => (bool) ($data['remember'] ?? false),
            ]);

            $this->redirect(route('two-factor.challenge'));

            return null;
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
