<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'tbl_leads'; // Specify the table name

    protected $fillable = [
        'contact_id',
        'source',
        'stage',
        'value',
        'assigned_to'
    ];

    /**
     * Define relationship with Contact model
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Define relationship with User model (Assigned Sales Representative)
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
