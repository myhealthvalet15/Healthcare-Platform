<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctorqualification extends Model
{
    use HasFactory;
    
    protected $table = 'doctor_qualification';
    protected $primaryKey = 'qualification_id';

    
    protected $fillable = ['qualification_id', 'qualification_name', 'qualification_type','active_status'];

  
    public $timestamps = false;
}
