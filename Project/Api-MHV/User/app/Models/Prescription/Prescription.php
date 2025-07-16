<?php

namespace App\Models\Prescription;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $table = 'prescription';
    protected $primaryKey = 'prescription_row_id';    
    protected $fillable = [
        'user_id', 
        'prescription_id', 
        'master_doctor_id', 
        'role_id', 
        'op_registry_id',
        'is_otc',
        'corporate_ohc_id',
        'template_id',
        'master_hcsp_user_id',
        'attachment_id',
        'is_conformance',
        'prescription_attachments',
        'doctor_notes',
        'user_notes',
        'share_with_patient',
        'case_id',
        'draft_save',
        'fav_pharmacy',
        'fav_lab',
        'prescription_date',
        'order_status',
        'created_by',
        'created_role',
        'modified_on',
        'corporate_location_id',
        'ohc',
        'alternate_drug',
        'active_status'
    ];  
    public $timestamps = true;
}
