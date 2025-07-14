<?php

namespace App\Models\V1Models\Drugs;


use Illuminate\Database\Eloquent\Model;

class Drugtype extends Model
{
   
    protected $table = 'drug_type';
    protected $primaryKey = 'id';    
    protected $fillable = ['id', 'drug_type_name', 'status','created_at'];  
    public $timestamps = true;
}
