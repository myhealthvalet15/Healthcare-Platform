<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCorporateFinancial extends Model
{
    use HasFactory;

    public $timestamps = false;


    protected $table = 'master_corporate_financials';

    protected $fillable = [
        'corporate_id',
        'location_id',
        'sgst',
        'cgst',
        'igst',
        'dlno',
        'tinno',
        'storeid',
        'tax_invoice_no',
        'discount',
    ];
}
