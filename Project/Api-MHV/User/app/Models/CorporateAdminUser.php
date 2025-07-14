<?php

namespace App\Models;

use App\Models\Department\CorporateHl1;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;

class CorporateAdminUser extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    protected $table = 'corporate_admin_user';

    protected $fillable = [
        'corporate_admin_user_id',
        'corporate_id',
        'location_id',
        'first_name',
        'last_name',
        'dob',
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
        'active_status',
        'department',
        'setting'
    ];
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getDepartmentNamesAttribute()
    {
        $departmentIds = explode(',', $this->department);
        return CorporateHl1::whereIn('hl1_id', $departmentIds)->pluck('hl1_name');
    }
}
