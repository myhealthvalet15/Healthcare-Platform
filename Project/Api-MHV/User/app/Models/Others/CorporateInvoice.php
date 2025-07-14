<?php

namespace App\Models\Others;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateInvoice extends Model
{
    use HasFactory;

    protected $table = 'corporate_invoice'; // Define table name
    protected $primaryKey = 'corporate_invoice_id';
    protected $fillable = [
        'corporate_id',
        'location_id',
        'corporate_user_id',
        'corporate_po_id',
        'po_number',
        'invoice_date',
        'invoice_number',
        'invoice_amount',
        'entry_date',
        'ohc_verify_date',
        'hr_verify_date',
        'ses_number',
        'ses_date',
        'head_verify_date',
        'ses_release_date',
        'submission_date',
        'payment_date',
        'cash_vendor',
        'cash_invoice_details',
        'invoice_status'
             
        
    ];
}
