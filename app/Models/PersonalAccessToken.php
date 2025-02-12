<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;


class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;

    protected $table = 'tbl_personal_access_tokens'; 

    protected $fillable = [
        'name', 
        'token', 
        'abilities', 
        'expires_at', 
        'tokenable_id', 
        'tokenable_type'
    ];

    protected $casts = [
        'abilities' => 'json', 
    ];

}
