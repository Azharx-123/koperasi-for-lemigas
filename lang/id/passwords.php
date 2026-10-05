<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Reset Password
    |--------------------------------------------------------------------------
    |
    | Baris berikut adalah baris bahasa default yang cocok dengan alasan
    | yang diberikan oleh broker password saat percobaan pembaruan
    | password gagal, misalnya karena token atau password yang tidak
    | valid. Dipakai oleh PasswordResetLinkController dan
    | NewPasswordController lewat helper __($status).
    |
    */

    'reset' => 'Password Anda telah berhasil diperbarui.',
    'sent' => 'Kami telah mengirimkan tautan reset password ke email Anda.',
    'throttled' => 'Mohon tunggu sebelum mencoba lagi.',
    'token' => 'Token reset password tidak valid atau sudah kedaluwarsa.',
    'user' => 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.',

];
