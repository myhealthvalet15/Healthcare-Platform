<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateMenuRights extends Model
{
    use HasFactory;
    protected $table = 'corporate_menu_rights'; 
    protected $primaryKey = 'id'; 
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'corporate_admin_user_id' ,
        'location_id' ,
        'landing_page' ,
        'employees' ,
        'employee_monitoring' ,
        'diagnostic_assessment',
        'hra' ,
        'stress_management' ,
        'pre_employment' ,
        'reports' ,
        'events' ,
        'health_partner' ,
        'corporate_user_id'
      ];

    public $incrementing = false; 
    protected $keyType = 'string';
    protected $casts = [
      'landing_page' => 'array',
  ];
}
