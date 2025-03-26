<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'tbl_leads'; // Specify the table name

    protected $fillable = [
        'name',
        'address',
        'email',
        'contact',
        'lead_source',
        'lead_status',
        'company_name',
        'company_website',
        'opportunity_amount',
        'description',
        'referred_by',
        'assigned_to',
    ];


    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class,'lead_id','id');
    }

   
    public function source()
    {
        return $this->belongsTo(Source::class, 'lead_source');
    }

   
    public function status()
    {
        return $this->belongsTo(Stage::class, 'lead_status');
    }


}
