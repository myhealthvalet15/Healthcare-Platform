<?php

namespace App\Models\Corporate\CorporateUsers;

use App\Models\Employee\EmployeeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class CorporateUserMapping extends Model
{
    use HasFactory;
    use HasApiTokens;
    public $timestamps = false; // Disables the default timestamp behavior
    // Table name, assuming it follows Laravel conventions
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
        'password',
        'password_changed',
        'mobile_country_code',
        'mobile_num',
        'super_admin',
        'signup_by',
        'signup_on',
        'aadhar',
        'age',
        'active_status'
    ];

  
   
}
