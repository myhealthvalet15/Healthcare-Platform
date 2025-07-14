<?php

namespace App\Models\V1Models\Corporate\CorporateComponents;;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateComponentSubmodules extends Model
{
    use HasFactory;
    protected $fillable = ['module_id', 'sub_module_id', 'sub_module_name'];
    
}
