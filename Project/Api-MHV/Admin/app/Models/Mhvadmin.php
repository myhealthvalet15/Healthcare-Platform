<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;
class Mhvadmin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'mhv_admin';
    protected $primaryKey = 'mhv_admin_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'admin_name',
        'email',
        'password',
        'verified',
        'verification_code',
        'verification_expires_at',
        'verification_attempts',
        'verification_attempts_locked_until',
        'verification_resend_attempts',
        'verification_resend_attempts_locked_until',
        'verification_resend_token',
        'last_password_reset_at',
        'password_reset_attempts',
        'password_reset_attempts_locked_until',
        'password_reset_token',
        'password_reset_token_expires_at'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'verification_resend_attempts_locked_until' => 'datetime',
        'verification_expires_at' => 'datetime'
    ];
}
