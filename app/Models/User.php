<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     * 
     */

    protected $table = "tbl_users";
    
    protected $fillable = [
        'name',
        'last_name',
        'profile_image',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function assignedContacts()
    {
        return $this->hasMany(Contact::class, 'assigned_to', 'id');
    }

    public function createdContacts()
    {
        return $this->hasMany(Contact::class, 'created_by', 'id');
    }

    public function assigntask()
    {
       return $this->hasMany(Tasks::class,'assigned_to','id');
    }


}
