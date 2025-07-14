<?php

namespace App\Models\V1Models\Medicalcondition;


use Illuminate\Database\Eloquent\Model;

class Medicalcondition extends Model
{
   
    protected $table = 'medical_condition';
    protected $primaryKey = 'condition_id';    
    protected $fillable = ['condition_id', 'condition_name', 'status','created_at'];  
    public $timestamps = true;
}
