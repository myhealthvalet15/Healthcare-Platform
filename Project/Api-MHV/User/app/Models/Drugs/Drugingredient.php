<?php

namespace App\Models\Drugs;

use Illuminate\Database\Eloquent\Model;

class Drugingredient extends Model
{
    protected $table = 'drug_ingredients';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'drug_ingredients', 'status','created_at'];
    public $timestamps = true;
}
