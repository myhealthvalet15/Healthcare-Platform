<?php

namespace App\Models\V1Models\Corporate\CorporateComponents;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateComponents extends Model
{
    use HasFactory;

    // Define the table associated with the model (if it's not plural)
    protected $table = 'corporate_components';

    // Specify the fillable fields
    protected $fillable = [
        'corporate_id',
        'module_id',
        'sub_module_id',
    ];

    // Cast the 'sub_module_id' field to an array
    protected $casts = [
        'sub_module_id' => 'array',  // This will store it as a JSON column in the database
    ];
}
