<?php

namespace App\Models\Prescription;

use Illuminate\Database\Eloquent\Model;

class PrescriptionTemplateDrug extends Model
{
    protected $table = 'prescription_template_drugs';
    protected $primaryKey = 'prescription_template_drugs_id';    
    protected $fillable = ['prescription_template_id', 'drug_template_id', 'intake_days', 'morning', 
    'afternoon', 'evening','night','intake_condition','remarks'];  
    public $timestamps = true;
}
