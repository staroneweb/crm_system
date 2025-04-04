<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opportunities extends Model
{
    use HasFactory;

    protected $table = 'tbl_opportunities';

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    
}
