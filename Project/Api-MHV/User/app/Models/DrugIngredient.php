<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugIngredient extends Model
{
    use HasFactory;

    protected $table = 'drug_ingredients';

    protected $fillable = [
        'drug_ingredients',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
