<?php
namespace App\Models\V1Models\Corporate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterUser extends Model
{
    use HasFactory;

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
        'mobile_hash',  
        'aadhar_hash',      
        'abha_hash',       
        'password'
    ];

    protected $hidden = [
        'password', 
    ];

    protected $casts = [
        'fromdate' => 'date',
    ];
}
