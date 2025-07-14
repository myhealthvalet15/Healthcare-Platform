<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodAllergy extends Model
{
    use HasFactory;

    protected $table = 'food_allergy';

    protected $fillable = [
        'food_name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
