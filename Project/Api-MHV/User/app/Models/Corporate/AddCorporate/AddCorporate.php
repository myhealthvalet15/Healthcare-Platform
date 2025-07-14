<?php

namespace App\Models\Corporate\AddCorporate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddCorporate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'add_corporate_excel_backup';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'file_name',
        'file_base64',
        'status',
        'denied_reason',
    ];

    /**
     * Status constants for the `status` field.
     */
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DENIED = 'denied';
    public const STATUS_PARTIAL = 'partial';

    /**
     * Get the status options.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_DENIED,
            self::STATUS_PARTIAL,
        ];
    }
}
