<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use HasFactory;

    protected $table = 'automation_rules';

    protected $fillable = ['name', 'trigger_event', 'action'];

    protected $casts = [
        'action' => 'array', // Automatically converts JSON to an array
    ];
    
}
