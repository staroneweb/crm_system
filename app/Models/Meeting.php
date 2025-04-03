<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $table = 'tbl_meetings';

    protected $fillable = [
        'lead_id',
        'meeting_date',
        'location',
        'agenda',
    ];

    /**
     * Relationship with Leads table.
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
