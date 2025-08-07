<?php

namespace App\Http\Controllers\Others;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function VendorList(Request $request)
    {
        $headerData = 'Invoice List Details';
        return view('content.Others.invoice', ['HeaderData' => $headerData]);
    }
    public function getInvoiceDetails(Request $request)
    {
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }

        // Get the from_date and to_date from the request
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        //$status  = $request->input('staus');
        //return $toDate;

        try {
            // Fetch data from the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllInvoiceDetails/' . $locationId, $request->all());

            // Get the data from the response
            $data = $response->json()['data'];
            // return $data;
            // Filter the data by the from_date and to_date
            if ($fromDate && $toDate) {
                $fromDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
                $toDate = \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');

                $data = array_filter($data, function ($record) use ($fromDate, $toDate) {
                    // Convert record date to 'Y-m-d' format
                    $recordDate = \Carbon\Carbon::parse($record['invoice_date'])->format('Y-m-d');
                    return $recordDate >= $fromDate && $recordDate <= $toDate;
                });
            }

            return response()->json([
                'result' => true,
                'data' => array_values($data) // Reset array keys after filtering
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }
    public function listVendor(Request $request)
    {
        $headerData = 'Vendor List Details';
        return view('content.Others.vendor', ['HeaderData' => $headerData]);
    }
    public function getVendorDetails(Request $request)
    {
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        try {
            // Fetch data from the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getVendorDetails/' . $locationId);

            // Get the data from the response
            $data = $response->json()['data'];
            if ($fromDate && $toDate) {
                $fromDate = \Carbon\Carbon::createFromFormat('d/m/Y', $fromDate)->format('Y-m-d');
                $toDate = \Carbon\Carbon::createFromFormat('d/m/Y', $toDate)->format('Y-m-d');

                $data = array_filter($data, function ($record) use ($fromDate, $toDate) {
                    // Convert record date to 'Y-m-d' format
                    $recordDate = \Carbon\Carbon::parse($record['po_date'])->format('Y-m-d');
                    return $recordDate >= $fromDate && $recordDate <= $toDate;
                });
            }

            // Filter the data by the from_date and to_date


            return response()->json([
                'result' => true,
                'data' => array_values($data) // Reset array keys after filtering
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }

    public function addVendor(Request $request)
    {

        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');
        if (!$locationId  || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        $requestData = $request->all();  // Get all request input data
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        $requestData['corporate_user_id'] = $userId;

        try {
            $validated = $request->validate([
                'vendor_name' => 'required|string',
                'po_number' => 'required|string',
                'po_value' => 'required|integer',
                'po_date' => 'required'

            ]);
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addVendor', $requestData);

            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => 'Vendor Added Successfully']);
            } else {
                return response()->json(['result' => false, 'message' => 'Error in adding Vendor', 'details' => $response->body()]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Fill all the details',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'error: ' . $e->getMessage()]);
        }
    }


    public function invoiceAdd(Request $request)
    {
        $headerData = 'Add New Invoice';
        return view('content.Others.add-invoice', ['HeaderData' => $headerData]);
    }

    public function insertInvoice(Request $request)
    {
        // Get session data
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }

        // Get all request input data
        $requestData = $request->all();
        $requestData['location_id'] = $locationId;  // Add location_id to the data
        $requestData['corporate_id'] = $corporateId;
        $requestData['corporate_user_id'] = $userId;

        // Determine which type of invoice is being processed (PO or Cash)
        $selectedInvoiceType = $request->input('selectedInvoiceType', 'po'); // Default to 'po' if not set

        try {
            // Define validation rules based on selectedInvoiceType
            $validationRules = [];
            if ($selectedInvoiceType == 'po') {
                $validationRules = [
                    'corporate_po_id' => 'required|integer',
                    'po_number' => 'required|string',
                    'invoice_date' => 'required',
                    'invoice_number' => 'required',
                    'invoice_amount' => 'required'

                    // You might want to adjust this validation for PO
                ];
            } elseif ($selectedInvoiceType == 'cash') {
                $validationRules = [
                    'cash_invoice_date' => 'required',
                    'cash_vendor' => 'required',
                    'cash_invoice_number' => 'required',
                    'cash_amount' => 'required',
                    'cash_invoice_details' => 'required',
                    'cash_entry_date' => 'required'

                ];
            }

            // Validate request data
            $validated = $request->validate($validationRules);

            // Make the API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addInvoice', $requestData);

            // Handle API response
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => 'Invoice Added Successfully']);
            } else {
                return response()->json(['result' => false, 'message' => 'Error in adding Vendor', 'details' => $response->body()]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => 'error',
                'message' => 'Fill all the details',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function invoiceEdit(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getInvoiceById/' . $id);

            if ($response->successful()) {
                $invoice = $response['data'];
                $headerData = 'Edit Invoice Details';
                return view('content.Others.edit-invoice', compact('invoice'), ['HeaderData' => $headerData]);
            } else {
                return redirect()->back()->with('error', 'An error occurred: ');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function updateInvoice(Request $request, $id)
    {
        // return $request;
        $locationId = session('location_id');
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');

        // Validate session data
        if (!$locationId || !$corporateId || !$userId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }

        // Determine which type of invoice is being processed (PO or Cash)
        $selectedInvoiceType = $request->input('selectedInvoiceType', 'po'); // Default to 'po' if not set
        //return $selectedInvoiceType;
        // Define common validation rules
        $validationRules = [
            'invoice_date' => 'required|date',
            'invoice_number' => 'required|string',
            'invoice_amount' => 'required|numeric',
        ];

        // Add type-specific validation rules
        if ($selectedInvoiceType === 'po') {

        } elseif ($selectedInvoiceType === 'cash') {
            $validationRules = array_merge($validationRules, [
                'cash_vendor' => 'required|string',
                'cash_invoice_details' => 'required|string',
                'cash_entry_date' => 'required|date',
            ]);
        }

        // Validate request data
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Prepare data for the API request
        $requestData = array_merge($request->all(), [
            'location_id' => $locationId,
            'corporate_id' => $corporateId,
            'corporate_user_id' => $userId,
        ]);

        try {
            // Make the API request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-user.hygeiaes.com/V1/corporate/corporate-components/updateInvoice/' . $id, $requestData);

            // Check if the response is successful
            if ($response->successful()) {
                return response()->json([
                    'result' => true,
                    'message' => 'Invoice updated successfully'
                ], 200);
            } else {
                return response()->json([
                    'result' => false,
                    'message' => 'Error from external API',
                    'details' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }


    public function getPoBalance(Request $request)
    {
        $locationId = session('location_id');
        if (!$locationId) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Request'
            ]);
        }
        //return $request;
        try {
            // Fetch data from the external API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getPoBalance/' . $locationId, $request->all());

            if ($response->successful()) {
                // Assuming the response is a JSON with 'result' and 'remainingBalance'
                $responseData = $response->json();

                // You can directly return the response from the external API
                // Ensure that the response is in the expected format for your front-end
                return response()->json([
                    'result' => $responseData['result'], // true or false
                    'remainingBalance' => $responseData['remainingBalance'], // PO balance
                ]);
            } else {
                // Handle if external API request failed
                return response()->json([
                    'result' => false,
                    'message' => 'Failed to fetch data from external API'
                ], 502);
            }


        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in Fetching data',
                'error' => $e->getMessage()
            ], 503);
        }
    }
}
