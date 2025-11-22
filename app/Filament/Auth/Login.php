<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Facades\Filament;
use Illuminate\Validation\ValidationException;
use Filament\Actions\Action;

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

    protected function authenticateUser(): ?User 
    {
        $data = $this->form->getState();
        
        // Coba login
        if (! Filament::auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'] ?? false)) {
        }

        $user = Filament::auth()->user();
        
        // Cek role setelah login berhasil
        if ($user && $user->role !== 'admin') {
            Filament::auth()->logout();
        }

        return $user;
    }
}