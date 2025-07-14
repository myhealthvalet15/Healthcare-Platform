<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestGroup extends Model
{
    use HasFactory;

    protected $table = 'test_group';
    protected $primaryKey = 'test_group_id';
    public $timestamps = true;

    protected $fillable = [
        'test_group_name',
        'group_type',
        'group_id',
        'subgroup_id',
        'active_status'
    ];

    // Hide created_at and updated_at in JSON responses
    protected $hidden = ['created_at', 'updated_at'];

    // Define relationships
    public function parentGroup()
    {
        return $this->belongsTo(TestGroup::class, 'group_id');
    }

    public function subGroup()
    {
        return $this->belongsTo(TestGroup::class, 'subgroup_id');
    }
}
