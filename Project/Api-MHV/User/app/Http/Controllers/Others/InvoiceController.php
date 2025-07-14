<?php

namespace App\Http\Controllers\Others;

use App\Http\Controllers\Controller;
use App\Models\Others\CorporatePO;
use App\Models\Others\CorporateInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function getAllInvoiceDetails($location_id, Request $request)
    {
        try {
            // Log the raw input values for debugging purposes
            Log::info('Request from_date: ' . $request->from_date);
            Log::info('Request to_date: ' . $request->to_date);
            Log::info('Request status: ' . $request->status);  // Log the status for debugging
    
            // Start building the query for CorporateInvoice with a left join with CorporatePO
            $query = CorporateInvoice::leftJoin('corporate_po', 'corporate_invoice.corporate_po_id', '=', 'corporate_po.corporate_po_id')
                ->where('corporate_invoice.location_id', $location_id);
    
            // Check for 'from_date' and filter accordingly
            if ($request->has('from_date') && $request->input('from_date')) {
                // Convert from DD/MM/YYYY to YYYY-MM-DD
                $fromDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
                Log::info('Converted from_date: ' . $fromDate);
                $query->whereDate('corporate_invoice.invoice_date', '>=', $fromDate);
            }
    
            // Check for 'to_date' and filter accordingly
            if ($request->has('to_date') && $request->input('to_date')) {
                // Convert from DD/MM/YYYY to YYYY-MM-DD
                $toDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');
                Log::info('Converted to_date: ' . $toDate);
                $query->whereDate('corporate_invoice.invoice_date', '<=', $toDate);
            }
    
            // Check for 'status' and filter accordingly
            if ($request->has('status') && $request->input('status')) {
                $status = $request->input('status');
                Log::info('Filter by status: ' . $status);
                $query->where('corporate_invoice.invoice_status', $status); // Assuming 'status' column exists in the table
            }
    
            // Select the desired columns from both tables, aliasing to avoid conflicts
            $ohc = $query->select(
                'corporate_invoice.*', 
                'corporate_po.vendor_name', 
                'corporate_po.po_number'
            )->get();
    
            // If no records were found, return a response with an empty array and a message
            if ($ohc->isEmpty()) {
                return response()->json([
                    'result' => true,
                    'message' => 'No records found for the given criteria',
                    'data' => [] // Empty data array for the frontend to handle
                ], 200); // 200 OK but with an empty data array
            }
    
            // Return the data with successful response
            return response()->json([
                'result' => true,
                'data' => $ohc
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error fetching invoice data: ' . $e->getMessage());
    
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
     

    

public function getVendorDetails($location_id, Request $request)
{
   
    try {
        // Fetch records based only on location_id
        $query = CorporatePO::where('location_id', $location_id);
        
        
        
        if ($request->has('from_date') && $request->input('from_date')) {
            // Convert from DD/MM/YYYY to YYYY-MM-DD
            $fromDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('from_date'))->format('Y-m-d');
            Log::info('Converted from_date: ' . $fromDate);
            $query->whereDate('corporate_po.po_date', '>=', $fromDate);
        }

        // Check for 'to_date' and filter accordingly
        if ($request->has('to_date') && $request->input('to_date')) {
            // Convert from DD/MM/YYYY to YYYY-MM-DD
            $toDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('to_date'))->format('Y-m-d');
            Log::info('Converted to_date: ' . $toDate);
            $query->whereDate('corporate_po.po_date', '>=', $toDate);
        }

        // Get the records
        $ohc = $query->get();

        return response()->json([
            'result' => true,
            'data' => $ohc
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'result' => false,
            'message' => 'An error occurred while fetching the OHC.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
public function addVendor(Request $request)
{
    $validator = Validator::make($request->all(), [
        'vendor_name' => 'required|string',
        'po_number' => 'required|string|unique:corporate_po,po_number', 
        'po_value' => 'required|integer',
        'po_date' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
    }

    $vendorData = $validator->validated();
    $vendorData['corporate_id'] = $request->corporate_id;
    $vendorData['location_id'] = $request->location_id;
    $vendorData['corporate_user_id'] = $request->corporate_user_id;
    $vendorData['po_date']  = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('po_date'))->format('Y-m-d');
               
    // Create the vendor data entry
    $vendor = CorporatePO::create($vendorData);

    return response()->json(['result' => true, 'data' => $vendor], 201);
}
 
    public function addInvoice(Request $request)
    {//
       // return $request;
    // Validate incoming request data based on selectedInvoiceType (po or cash)
    $selectedInvoiceType = $request->input('selectedInvoiceType', 'po'); // Default to 'po' if not set

    // Initialize $invoiceData to hold the validated data
    $invoiceData = [];

    if ($selectedInvoiceType == 'po') {
        // Define validation rules for 'po' type
        $validator = Validator::make($request->all(), [
            'corporate_po_id' => 'required|integer',
            'po_number' => 'required|string',
            'invoice_date' => 'required|date',
            'invoice_number' => 'required|string',
            'invoice_amount' => 'required|numeric',
           
        ]);

        $invoiceData = $validator->validated();

        // Attach additional data from request for PO
        $invoiceData['corporate_id'] = $request->corporate_id;
        $invoiceData['po_number'] = $request->po_number;
        $invoiceData['location_id'] = $request->location_id;
        $invoiceData['corporate_user_id'] = $request->corporate_user_id;
        $invoiceData['cash_vendor'] = null;
        $invoiceData['cash_invoice_details'] = null;
        $invoiceData['invoice_status'] = null;
        
    } elseif ($selectedInvoiceType == 'cash') {
        // Define validation rules for 'cash' type
        $validator = Validator::make($request->all(), [
            'cash_invoice_date' => 'required|date',
            'cash_vendor' => 'required|string',
            'cash_invoice_number' => 'required|string',
            'cash_amount' => 'required|numeric',
            'cash_invoice_details' => 'required|string',
            'cash_entry_date' => 'required|date',
           
        ]);

        $invoiceData = $validator->validated();

        // Attach additional data from request for Cash
        $invoiceData['corporate_id'] = $request->corporate_id;
       
        $invoiceData['location_id'] = $request->location_id;
        $invoiceData['corporate_user_id'] = $request->corporate_user_id;
        $invoiceData['invoice_date'] = $request->cash_invoice_date;
        $invoiceData['invoice_number'] = $request->cash_invoice_number;
        $invoiceData['entry_date'] = $request->cash_entry_date;
        $invoiceData['invoice_amount'] = $request->cash_amount;
        $invoiceData['invoice_status'] = 11;

        // Set the fields that are not needed for the 'cash' invoice type to null
        $invoiceData['corporate_po_id'] = null;
        $invoiceData['invoice_amount'] = null;
       
        $invoiceData['ohc_verify_date'] = null;
        $invoiceData['hr_verify_date'] = null;
        $invoiceData['ses_number'] = null;
        $invoiceData['ses_date'] = null;
        $invoiceData['head_verify_date'] = null;
        $invoiceData['ses_release_date'] = null;
        $invoiceData['submission_date'] = null;
        $invoiceData['payment_date'] = null;
        
    } else {
        // If an unsupported invoice type is selected, return an error
        return response()->json(['result' => false, 'message' => 'Invoice type not supported'], 400);
    }

    // If validation fails, return errors
    if ($validator->fails()) {
        return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
    }
    $invoiceData['invoice_date'] = Carbon::createFromFormat('d-m-Y', $request->invoice_date)->format('Y-m-d');
    $invoiceData['po_number'] = $request->po_number;
    Log::info('Request of add invoice: ' ,$invoiceData);
    // Create the invoice based on the validated and prepared data
    $invoice = CorporateInvoice::create($invoiceData);

    // Return a successful response with the created invoice data
    return response()->json(['result' => true, 'data' => $invoice], 201);
}
public function getInvoiceById($id)
{
    //return $id;
    try {
        //$invoice = CorporateInvoice::where('corporate_invoice_id', $id)->first(); // Retrieve single record for given ID
        $invoice = CorporateInvoice::where('corporate_invoice_id', $id)
    ->join('corporate_po', 'corporate_invoice.corporate_po_id', '=', 'corporate_po.corporate_po_id')
    ->select('corporate_invoice.*', 'corporate_po.vendor_name')
    ->first();
        if (!$invoice) {
            return response()->json(['message' => 'invoice not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $invoice, // Return the required drug template data
        ]);
    } catch (\Exception $e) {
        //Log::error('Failed to retrieve inventory', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to retrieve invoice'], 500);
    }
}

public function updateInvoice(Request $request, $corporate_invoice_id)
{
    $invoice = CorporateInvoice::where('corporate_invoice_id', $corporate_invoice_id)->first();
    if (!$invoice) {
        return response()->json(['message' => 'Invoice not found'], 404);
    }

    $selectedInvoiceType = $request->input('selectedInvoiceType', 'po'); // Default to 'po'

    // Handle PO type invoice
    if ($selectedInvoiceType == 'po') {
        $validator = Validator::make($request->all(), [
            'invoice_date' => 'required|date_format:d-m-Y', // Accept dd-mm-yyyy format
            'invoice_number' => 'required|string',
            'invoice_amount' => 'required|numeric',
            'entry_date' => 'required|date_format:d-m-Y', // Accept dd-mm-yyyy format
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get validated data
        $invoiceData = $validator->validated();

        // Convert invoice_date and entry_date to Y-m-d format
        $invoiceData['invoice_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $invoiceData['invoice_date'])->format('Y-m-d');
        $invoiceData['entry_date'] = \Carbon\Carbon::createFromFormat('d-m-Y', $invoiceData['entry_date'])->format('Y-m-d');
        $entryDate = $invoiceData['entry_date'];  // Store entry date for later comparison

        // Process other date-related fields
        if ($request->has('ohc_verify_date')) {
            $ohcVerifyDate = \Carbon\Carbon::parse($request->input('ohc_verify_date'))->format('Y-m-d');
            if ($ohcVerifyDate < $entryDate) {
                return response()->json(['message' => 'OHC verification date cannot be earlier than the entry date'], 422);
            }
            $invoice->update(['ohc_verify_date' => $ohcVerifyDate]);
            $invoice->update(['invoice_status' => 2]);
        }

        if ($request->has('hr_verify_date')) {
            $hrVerifyDate = \Carbon\Carbon::parse($request->input('hr_verify_date'))->format('Y-m-d');
            if ($hrVerifyDate < $ohcVerifyDate) {
                return response()->json(['message' => 'HR verification date cannot be earlier than the OHC verification date'], 422);
            }
            $invoice->update(['hr_verify_date' => $hrVerifyDate]);
            $invoice->update(['invoice_status' => 3]);
        }

        if ($request->has('ses_date')) {
            $sesDate = \Carbon\Carbon::parse($request->input('ses_date'))->format('Y-m-d');
            if ($sesDate < $hrVerifyDate) {
                return response()->json(['message' => 'SES date cannot be earlier than the HR verification date'], 422);
            }
            $invoice->update(['ses_date' => $sesDate]);
            $invoice->update(['invoice_status' => 4]);
        }

        if ($request->has('head_verify_date')) {
            $headVerifyDate = \Carbon\Carbon::parse($request->input('head_verify_date'))->format('Y-m-d');
            if ($headVerifyDate < $sesDate) {
                return response()->json(['message' => 'Dept. Head verification date cannot be earlier than the SES date'], 422);
            }
            $invoice->update(['head_verify_date' => $headVerifyDate]);
            $invoice->update(['invoice_status' => 5]);
        }

        if ($request->has('ses_release_date')) {
            $sesReleaseDate = \Carbon\Carbon::parse($request->input('ses_release_date'))->format('Y-m-d');
            if ($sesReleaseDate < $headVerifyDate) {
                return response()->json(['message' => 'SES release date cannot be earlier than the Dept. Head verification date'], 422);
            }
            $invoice->update(['ses_release_date' => $sesReleaseDate]);
            $invoice->update(['invoice_status' => 6]);
        }

        if ($request->has('submission_date')) {
            $submissionDate = \Carbon\Carbon::parse($request->input('submission_date'))->format('Y-m-d');
            if ($submissionDate < $sesReleaseDate) {
                return response()->json(['message' => 'Bill submission date cannot be earlier than the SES release date'], 422);
            }
            $invoice->update(['submission_date' => $submissionDate]);
            $invoice->update(['invoice_status' => 7]);
        }

        if ($request->has('payment_date')) {
            $paymentDate = \Carbon\Carbon::parse($request->input('payment_date'))->format('Y-m-d');
            if ($paymentDate < $submissionDate) {
                return response()->json(['message' => 'Payment date cannot be earlier than the bill submission date'], 422);
            }
            $invoice->update(['payment_date' => $paymentDate]);
            $invoice->update(['invoice_status' => 8]);
        }

        // Update the rest of the fields for 'po'
        $invoice->update([
            'invoice_date' => $invoiceData['invoice_date'],
            'invoice_number' => $invoiceData['invoice_number'],
            'invoice_amount' => $invoiceData['invoice_amount'],
            'entry_date' => $invoiceData['entry_date'],
        ]);
    }
    // Handle Cash type invoice similarly
    elseif ($selectedInvoiceType == 'cash') {
        // Handle 'cash' type logic here (similar to 'po' type)
    }
    // Handle invalid invoice type
    else {
        return response()->json(['result' => false, 'message' => 'Invoice type not supported'], 400);
    }

    return response()->json(['message' => 'Invoice updated successfully'], 200);
}
// In InvoiceController.php
public function getPoBalance(Request $request)
{
    // Validate input parameters
    $validator = Validator::make($request->all(), [
        'corporate_po_id' => 'required|integer',
        'po_number' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['result' => false, 'message' => 'Invalid data'], 400);
    }

    // Retrieve the PO value from the purchase_orders table using Eloquent
    $purchaseOrder = CorporatePO::where('corporate_po_id', $request->corporate_po_id)
        ->where('po_number', $request->po_number)
        ->first(); // Use first() instead of value()

    if (!$purchaseOrder) {
        return response()->json(['result' => false, 'message' => 'PO not found'], 404);
    }

    // Get the total PO value from the retrieved purchase order
    $poValue = $purchaseOrder->po_value;

    // Calculate the total used PO value by summing invoice amounts for the specific PO
    $poBalanceUsed = CorporateInvoice::where('corporate_po_id', $request->corporate_po_id)
        ->where('po_number', $request->po_number)
        ->sum('invoice_amount'); // Sum of invoice amounts for the PO

    // Calculate the remaining balance
    $remainingBalance = $poValue - $poBalanceUsed;

    // Return the response with the remaining balance
    return response()->json([
        'result' => true,
        'remainingBalance' => $remainingBalance
    ]);
    
}


}
