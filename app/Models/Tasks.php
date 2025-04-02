<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $table = 'tbl_tasks';

    protected $fillable = [
        'lead_id',
        'task_name',
        'task_description',
        'start_datetime',
        'duration',
        'status_id',
        'assigned_to',
    ];


    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function status()
    {
        return $this->belongsTo(LeadStatus::class, 'status_id');
    }



}
