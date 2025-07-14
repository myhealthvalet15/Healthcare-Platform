<?php

namespace App\Models\Others;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BioMedicalWaste extends Model
{
    use HasFactory;

    protected $table = 'industry_waste'; // Define table name
    protected $primaryKey = 'industry_id';
    protected $fillable = [
        'date',
        'red',
        'yellow',
        'blue',
        'white',
        'issued_by',
        'corp_id',
        'loc_id',
        'received_by'
       
        
    ];
}
