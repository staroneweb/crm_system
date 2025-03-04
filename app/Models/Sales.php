<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'tbl_sales';

    public function leads()
    {
        return $this->belongsTo(Lead::class,'lead_id');
    }

}
