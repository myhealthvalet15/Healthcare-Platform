<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outpatient extends Model
{
    use HasFactory;
    protected $table = 'outpatient_component';
    protected $primaryKey = 'op_component_id';

    
    protected $fillable = ['op_component_id', 'op_component_name', 'op_component_type','active_status'];

  
    public $timestamps = false;
}
