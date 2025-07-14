<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthParameters extends Model
{
    use HasFactory;
    protected $table = 'health_parameters';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'user_id',
        'blood_group_with_rh_factor',
        'height',
        'weight',
        'health_color',
        'dashboard_parameters',
        'descriptive_mark',
        'allergic_food',
        'allergic_ingredients',
        'published_conditions',
        'unpublished_conditions'
    ];
    protected $casts = [
        'allergic_food' => 'array',
        'allergic_ingredients' => 'array',
        'published_conditions' => 'array',
        'unpublished_conditions' => 'array'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
