<?php

namespace App\Models\V1Models\Hra\Factors;

use Illuminate\Database\Eloquent\Model;

class HraFactor extends Model
{
    // No need for $fillable or $guarded if you're not using mass assignment
    protected $primaryKey = 'factor_id'; // Correct primary key column
}
