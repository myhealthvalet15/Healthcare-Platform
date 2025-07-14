<?php

namespace App\Models\V1Models\Forms;


use Illuminate\Database\Eloquent\Model;

class CorporateForms extends Model
{
   
    protected $table = 'corporate_forms'; 
    protected $primaryKey = 'corporate_form_id';    
    protected $fillable = ['corporate_form_id', 'form_name', 'state','status'];  
    public $timestamps = true;
}
