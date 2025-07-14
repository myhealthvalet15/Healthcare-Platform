<?php

namespace App\Models\Corporate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Laravel\Passport\HasApiTokens;

class MasterUser extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    protected $table = 'master_user';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'email',
        'mob_country_code',
        'mob_num',
        'aadhar_id',
        'abha_id',
        'email_hash',
        'first_name_hash',
        'last_name_hash',
        'aadhar_hash',
        'abha_hash',
        'mobile_hash',
        'password',
        'user_profile_img',
        'user_banner_img',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'fromdate' => 'date',
    ];
    public function employeeUserMappings()
    {
        return $this->hasMany(\App\Models\Corporate\EmployeeUserMapping::class, 'user_id', 'user_id');
    }
}
