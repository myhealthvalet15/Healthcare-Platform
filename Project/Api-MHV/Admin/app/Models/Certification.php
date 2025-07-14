<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;
    protected $primaryKey = 'certificate_id';

    protected $table = 'certification';  
    public $timestamps = false;


    protected $fillable = [
        'corporate_id', 
        'certification_title', 
        'short_tag', 
        'content', 
        'condition', 
        'color_condition', 
        'active_status'
    ];
    protected $casts = [
        'condition' => 'array',
        'color_condition' => 'array',
    ];
}
