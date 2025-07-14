<?php

namespace App\Models\Prescription;

use Illuminate\Database\Eloquent\Model;

class PrescriptionTemplate extends Model
{
    protected $table = 'prescription_template';
    protected $primaryKey = 'prescription_template_id';    
    protected $fillable = ['location_id', 'ohc_id', 'pharmacy_id', 'template_name', 
    'created_on', 'created_by'];  
    public $timestamps = true;
}
