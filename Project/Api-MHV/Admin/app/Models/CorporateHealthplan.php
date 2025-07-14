<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateHealthplan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'corporate_healthplan';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'corporate_healthplan_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'corporate_healthplan_id',
        'corporate_id',
        'healthplan_title',
        'healthplan_description',
        'master_test_id',
        'certificate_id',
        'isPreEmployement',
        'created_by',
        'modified_by',
        'created_date',
        'modified_date',
        'forms',
        'gender',
        'active_status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'master_test_id' => 'array',
        'certificate_id' => 'array',
        'isPreEmployement' => 'boolean',
        'active_status' => 'boolean',
        'created_date' => 'datetime',
        'modified_date' => 'datetime'
    ];

    /**
     * Define the relationship with the master_corporate table.
     */
    public function masterCorporate()
    {
        return $this->belongsTo(MasterCorporate::class, 'corporate_id', 'corporate_id');
    }
}
