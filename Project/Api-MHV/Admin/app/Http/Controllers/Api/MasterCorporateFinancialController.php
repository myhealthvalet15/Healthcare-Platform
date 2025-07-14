<?php

namespace App\Http\Controllers;

use App\Models\MasterCorporateFinancial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class MasterCorporateFinancialController extends Controller
{
    public function index()
    {
        return response()->json(MasterCorporateFinancial::all(), 200);
    }

    public function store(Request $request)
    {
        // Log::info($request->all());
        try {
            // Custom validation rules using Validator facade
            $validator = Validator::make($request->all(), [
                'location_id' => 'required|integer',
                'sgst' => 'required|integer',
                'cgst' => 'required|integer',
                'igst' => 'required|integer',
                'dlno' => 'nullable|string|max:255',
                'tinno' => 'nullable|string|max:255',
                'storeid' => 'nullable|string|max:255',
                'tax_invoice_no' => 'nullable|string|max:255',
                'discount' => 'required|integer',
            ]);

            // If validation fails, throw an exception
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Proceed with creating the new record
            $financial = MasterCorporateFinancial::create($request->only([
                'location_id',
                'sgst',
                'cgst',
                'igst',
                'dlno',
                'tinno',
                'storeid',
                'tax_invoice_no',
                'discount'
            ]));

            return response()->json(['message' => 'Record created successfully', 'data' => $financial], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create financial record', 'error' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        $financial = MasterCorporateFinancial::find($id);

        if (!$financial) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($financial, 200);
    }

    public function update(Request $request, $id)
    {
        $financial = MasterCorporateFinancial::find($id);

        if (!$financial) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $request->validate([
            'location_id' => 'sometimes|required|integer',
            'sgst' => 'sometimes|required|integer',
            'cgst' => 'sometimes|required|integer',
            'igst' => 'sometimes|required|integer',
            'dlno' => 'nullable|string|max:255',
            'tinno' => 'nullable|string|max:255',
            'storeid' => 'nullable|string|max:255',
            'tax_invoice_no' => 'nullable|string|max:255',
            'discount' => 'sometimes|required|integer',
        ]);

        $financial->update($request->all());
        return response()->json($financial, 200);
    }

    public function destroy($id)
    {
        $financial = MasterCorporateFinancial::find($id);

        if (!$financial) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $financial->delete();
        return response()->json(['message' => 'Record deleted successfully'], 200);
    }
}
