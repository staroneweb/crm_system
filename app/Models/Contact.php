<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = "tbl_contacts";

    public function assigned(){

        return $this->belongsTo(User::class,'assigned_to','id');
    }

    public function createdby(){

        return $this->belongsTo(User::class,'created_by','id');

    }

}
