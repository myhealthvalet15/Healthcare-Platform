<?php

namespace App\Models\Prescription;

use Illuminate\Database\Eloquent\Model;

class PrescriptionDetails extends Model
{
    protected $table = 'prescription_details';
    protected $primaryKey = 'prescription_details_id';    
    protected $fillable = [
        'prescription_row_id', 
        'drug_name', 
        'drug_template_id', 
        'to_issue', 
        'remaining_medicine',
        'substitute_drug',
        'prescribed_days',
        'early_morning',
        'morning',
        'late_morning',
        'afternoon',
        'late_afternoon',
        'evening',
        'night',
        'late_night',
        'drug_type',
        'intake_condition',
        'remarks',
        'created_at', 
        'prescription_type',
        'alternate_drug',
        'alternate_quantity',
        'created_by'];  
    public $timestamps = true;
}
