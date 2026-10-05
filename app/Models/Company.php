<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'description',
        'vision',
        'mission',
        'history',
        'email',
        'phone',
        'address',
        'logo',
        'image',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => cache()->forget('company_info'));
        static::deleted(fn () => cache()->forget('company_info'));
    }
}
