<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    protected $table = 'tbl_password_reset_tokens';

    protected $fillable = [
        'email', 'token', 'created_at', 'expires_at',
    ];
}
