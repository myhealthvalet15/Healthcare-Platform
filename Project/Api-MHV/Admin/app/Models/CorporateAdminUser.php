<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateAdminUser extends Model
{
    use HasFactory;
    protected $table = 'corporate_admin_user';

    protected $fillable = [
        'corporate_admin_user_id',
        'corporate_id',
        'location_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'email_hash',
        'first_name_hash',
        'last_name_hash',
        'password',
        'mobile_country_code',
        'mobile_num',
        'created_on',
        'createdby',
        'password_changed',
        'super_admin',
        'signup_by',
        'signup_role',
        'signup_type',
        'signup_on',
        'aadhar',
        'age',
        'active_status'
    ];
}
