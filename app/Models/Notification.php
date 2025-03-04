<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    use HasFactory;

    protected $fillable = ['user_id', 'message', 'is_read'];

    public $timestamps = false; // Since created_at has a default timestamp


}
