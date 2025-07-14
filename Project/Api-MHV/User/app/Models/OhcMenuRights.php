<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OhcMenuRights extends Model
{
  use HasFactory;
  protected $table = 'ohc_menu_rights';
  protected $primaryKey = 'id';
  const CREATED_AT = 'created_at';
  const UPDATED_AT = 'updated_at';
  protected $fillable = [
    'corporate_admin_user_id',
    'location_id',
    'doctor',
    'qualification_id ',
    'pharmacy_id ',
    'ohc_dashboard',
    'out_patient',
    'prescription',
    'tests',
    'stocks',
    'ohc_report',
    'census_report',
    'safety_board',
    'invoice',
    'bio_medical',
    'inventory',
    'forms',
    'corporate_user_id'
  ];

  public $incrementing = false;
  protected $keyType = 'string';
  protected $casts = [
    'landing_page' => 'array',
  ];
}
