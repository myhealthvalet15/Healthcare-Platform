<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCorporateUser extends Model
{
    use HasFactory;

    protected $table = 'master_corporate_user'; 
    protected $primaryKey = 'id'; 
    public $incrementing = true; 
    public $timestamps = false;


    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'email',
        'password',
        'mobile_country_code',
        'mobile_num',
        'created_on',
        'createdby',
        'isactive',
        'ispasswordchanged',
        'super_admin',
        'signup_by',
        'signup_role',
        'signup_type',
        'signup_on',
        'aadhar',
        'age',
    ];

    protected $hidden = [
        'password', 
    ];

    protected $dates = [
        'dob',
        'created_on',
        'signup_on',
    ];
}
