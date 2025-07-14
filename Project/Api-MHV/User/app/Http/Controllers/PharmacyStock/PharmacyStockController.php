<?php

namespace App\Http\Controllers\PharmacyStock;

use App\Models\PharmacyStock\PharmacyStock;
//use App\Models\Corporate\CorporatePharmacy;
use App\Models\Drugs\Drugtype;
use App\Models\Drugs\Drugingredient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PharmacyStockController extends Controller
{
    public function getAllPharmacyStock($location_id)
    {
        try {
            // Step 1: Fetch the ohc_pharmacy_id where main_pharmacy = 1 and location_id matches
            $ohc_pharmacy_ids = DB::table('corporate_ohc_pharmacy')
                ->where('corporate_ohc_pharmacy.location_id', $location_id)
                ->where('corporate_ohc_pharmacy.main_pharmacy', 1)
                ->pluck('corporate_ohc_pharmacy.ohc_pharmacy_id'); // Get the ohc_pharmacy_id(s)

            if ($ohc_pharmacy_ids->isEmpty()) {
                return response()->json([
                    'result' => false,
                    'message' => 'No records found for the given location or main pharmacy.'
                ], 404);
            }

            // Step 2: Join the tables and filter pharmacy_stock by the obtained ohc_pharmacy_ids
            $pharmacy_stock = PharmacyStock::join('drug_template', 'pharmacy_stock.drug_template_id', '=', 'drug_template.drug_template_id')
                ->whereIn('pharmacy_stock.ohc_pharmacy_id', $ohc_pharmacy_ids) // Filter by ohc_pharmacy_id
                ->where('pharmacy_stock.quantity', '>', 0)
                ->select('pharmacy_stock.*', 'drug_template.*') // Select only the necessary columns
                ->get();

            return response()->json([
                'result' => true,
                'data' => $pharmacy_stock
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Pharmacy Stock.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllDrugTemplateDetails(Request $request)
    {
        try {
            // Fetch drug templates with all fields
            $drugTemplates = DB::table('drug_template')
                ->join('corporate_ohc_pharmacy', 'drug_template.location_id', '=', 'corporate_ohc_pharmacy.location_id') // Join on location_id
                ->where('corporate_ohc_pharmacy.main_pharmacy', 1) // Filter where main_pharmacy = 1
                ->get();

            foreach ($drugTemplates as &$template) {
                if (!empty($template->drug_ingredient)) {
                    // Convert stored ingredient IDs (comma-separated) into an array
                    $ingredientIds = explode(',', $template->drug_ingredient);

                    // Fetch the actual ingredient names
                    $ingredients = DB::table('drug_ingredients')
                        ->whereIn('id', $ingredientIds)
                        ->pluck('drug_ingredients') // Get only ingredient names
                        ->toArray();

                    // Store ingredient names as a comma-separated string
                    $template->ingredient_names = implode(', ', $ingredients);
                } else {
                    $template->ingredient_names = 'N/A';
                }
            }

            return response()->json([
                'drugTemplate' => $drugTemplates
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve drug templates with ingredients', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to retrieve drug template details'], 500);
        }
    }
    public function addPharmacyStock(Request $request)
    {
        //Log::info('Bhava Received Request:', $request->all());
        try {
            $drugTemplateData['ohc_pharmacy_id'] = $request->ohc_pharmacy_id;
            $drugTemplateData['drug_name'] = $request->drug_name;
            $drugTemplateData['drug_template_id'] = $request->drug_template_id;
            $drugTemplateData['drug_batch'] = $request->drug_batch;
            $drugTemplateData['manufacter_date'] = Carbon::createFromFormat('d/m/Y', $request->manufacture_date)->format('Y-m-d');
            $drugTemplateData['expiry_date'] = Carbon::createFromFormat('d/m/Y', $request->expiry_date)->format('Y-m-d');
            $drugTemplateData['drug_type'] = $request->drug_type;
            $drugTemplateData['drug_strength'] = $request->drug_strength;
            $drugTemplateData['quantity'] = (int) $request->quantity;
            $drugTemplateData['current_availability'] = (int) $request->current_availability;
            // $drugTemplateData['tablet_in_strip'] = (int) $request->tablet_in_strip;

            // Convert values properly
            $drugTemplateData['amount_per_tab'] = floatval($request->amount_per_tab ?? 0);
            $drugTemplateData['sold_quantity'] = (int) $request->sold_quantity;
            //$drugTemplateData['discount'] = (float) $request->discount;
            $drugTemplateData['sgst'] = floatval($request->sgst ?? 0);
            $drugTemplateData['cgst'] = floatval($request->cgst ?? 0);
            $drugTemplateData['igst'] = floatval($request->igst ?? 0);
            $drugTemplateData['ohc'] = $request->ohc;
            $drugTemplateData['master_pharmacy_id'] = null;
            Log::info('PharmacyStock - Data Before Insert:', $drugTemplateData);

            // Calculate total cost properly
            $drugTemplateData['total_cost'] = $drugTemplateData['quantity'] * $drugTemplateData['amount_per_tab'];

            // Debugging (optional)
            // Log::info('Final Data Before Insert:', $drugTemplateData);
            // Insert data
            $drugTemplate = PharmacyStock::create($drugTemplateData);
            Log::info('PharmacyStock - Data After Insert:', $drugTemplate->toArray());
            return response()->json(['result' => true, 'data' => $drugTemplate], 201);
        } catch (\Exception $e) {
            Log::error('PharmacyStock - Error inserting:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json(['result' => false, 'message' => 'An error occurred while saving the drug template.'], 500);
        }
    }

    public function getDrugTypesAndIngredients(Request $request)
    {
        try {
            $drugTypes = Drugtype::all();
            $drugIngredients = Drugingredient::all();
            return response()->json([
                'drugTypes' => $drugTypes,
                'drugIngredients' => $drugIngredients
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve drug types and ingredients'], 500);
        }
    }
    public function updatePharmacyStock(Request $request, $id)
    {
        //return 'Heklo';
        // Log::info('Bhavas Update Request Data:', $request->all());

        try {
            $pharmacystock = PharmacyStock::findOrFail($id);
            //return $pharmacystock;
            $pharmacystockData['quantity'] = (int) $request->quantity;
            $pharmacystockData['drug_batch'] = $request->drug_batch;
            $pharmacystockData['manufacter_date'] = Carbon::createFromFormat('Y-m-d', $request->manufacture_date)->format('Y-m-d');
            $pharmacystockData['expiry_date'] = Carbon::createFromFormat('Y-m-d', $request->expiry_date)->format('Y-m-d');
            $pharmacystockData['current_availability'] = $request->current_availability;


            // Log::info('Pharmacy Stock Data not being updated:', $pharmacystockData);
            $pharmacystock->update($pharmacystockData);

            return response()->json(['result' => true, 'data' => $pharmacystock], 200);
        } catch (\Exception $e) {
            // Log the exception message with more details
            Log::error('Failed to Update Pharmacy Stock', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->all()  // Log the request data
            ]);

            return response()->json(['result' => false, 'message' => 'An error occurred while updating pharmacy stocddk.'], 500);
        }
    }


    public function getMainStockDetails($drug_template_id)
    {
        try {
            $pharmacyStock = PharmacyStock::where('drug_template_id', $drug_template_id)->first();
            //return  $pharmacyStock;
            return response()->json([
                'result' => true,
                'data' => $pharmacyStock
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Pharmacy Stock.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function moveStocktoMainStore(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'drug_template_id' => 'required|integer',
                'drug_name' => 'required|string',
                'drug_strength' => 'required|string',
                'drug_batch' => 'required|string',
                'manufacter_date' => 'required|date',
                'expiry_date' => 'required|date',
                'drug_type' => 'required|integer',
                'quantity' => 'required|integer',
                'current_availability' => 'required|integer',
                'sold_quantity' => 'required|integer',
                'ohc_pharmacy_id' => 'required|integer',  // Destination
                'amount_per_tab' => 'required',
                'sgst' => 'required',
                'igst' => 'required',
                'cgst' => 'required',
                'total_cost' => 'required',
                'mainStoreId' => 'required|integer', // This is the main store's ohc_pharmacy_id
            ]);

            $mainstoreId = $validatedData['mainStoreId'];
            $drugTemplateId = $validatedData['drug_template_id'];

            // Add shared values
            $validatedData['ohc'] = 1;
            $validatedData['created_at'] = now();
            $validatedData['updated_at'] = now();

            Log::info('Initiating stock move:', $validatedData);

            // ✅ Get main store stock
            $mainStoreStock = PharmacyStock::where('ohc_pharmacy_id', $mainstoreId)
                ->where('drug_template_id', $drugTemplateId)
                ->first();

            if (!$mainStoreStock) {
                return response()->json(['result' => false, 'message' => 'Main store stock for the drug not found.'], 404);
            }

            // ✅ Check stock availability
            if ($mainStoreStock->current_availability < $validatedData['quantity']) {
                return response()->json([
                    'result' => false,
                    'message' => 'Insufficient stock in the main store.'
                ], 400);
            }

            // ✅ Update main store stock
            $mainStoreStock->current_availability -= $validatedData['quantity'];
            $mainStoreStock->sold_quantity += $validatedData['quantity'];
            $mainStoreStock->updated_at = now();
            $mainStoreStock->save();

            Log::info("Main store stock updated. Main Store OHC ID: $mainstoreId, Drug Template ID: $drugTemplateId");

            // ✅ Create destination pharmacy stock manually (and get the inserted ID)
            $pharmacyStockId = DB::table('pharmacy_stock')->insertGetId([
                'drug_name' => $validatedData['drug_name'],
                'drug_template_id' => $validatedData['drug_template_id'],
                'drug_batch' => $validatedData['drug_batch'],
                'manufacter_date' => $validatedData['manufacter_date'],
                'expiry_date' => $validatedData['expiry_date'],
                'drug_type' => $validatedData['drug_type'],
                'drug_strength' => $validatedData['drug_strength'],
                'quantity' => $validatedData['quantity'],
                'current_availability' => $validatedData['current_availability'],
                'sold_quantity' => $validatedData['sold_quantity'],
                'ohc_pharmacy_id' => $validatedData['ohc_pharmacy_id'],
                'amount_per_tab' => $validatedData['amount_per_tab'],
                'sgst' => $validatedData['sgst'],
                'igst' => $validatedData['igst'],
                'cgst' => $validatedData['cgst'],
                'total_cost' => $validatedData['total_cost'],
                'ohc' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info("New pharmacy stock inserted. ID: $pharmacyStockId");

            // ✅ Insert into drug_stock_sold
            DB::table('drug_stock_sold')->insert([
                'pharmacy_stock_id' => $pharmacyStockId,
                'quantity' => $validatedData['quantity'],
                'drug_value' => $drugTemplateId,
                'master_user_id' => 0,
                'prescription_id' => 0,
                'ohc' => 1,
                'ohc_pharmacy_id' => $mainstoreId,
                'move_to' => $validatedData['ohc_pharmacy_id'],
                'pharmacy_walkin' => 0,
                'created_by' => auth('api')->id() ?? 0,
                'created_on' => now(),
                'master_pharmacy_id' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info("Insert into drug_stock_sold successful for pharmacy_stock_id: $pharmacyStockId");

            return response()->json(['result' => true, 'pharmacy_stock_id' => $pharmacyStockId], 201);

        } catch (\Exception $e) {
            Log::error('Error while moving stock: ' . $e->getMessage());
            return response()->json(['result' => false, 'message' => 'An error occurred while moving the stock.'], 500);
        }
    }


    public function getSubPharmacyStockById($storeId)
    {
        //return $storeId;
        try {
            // Fetch records from pharmacy_stock table based on ohc_pharmacy_id
            $pharmacy_stock = PharmacyStock::join('drug_template', 'pharmacy_stock.drug_template_id', '=', 'drug_template.drug_template_id')
                ->where('pharmacy_stock.ohc_pharmacy_id', $storeId) // Filter by ohc_pharmacy_id (single storeId)
                ->where('pharmacy_stock.quantity', '>', 0)
                ->select('pharmacy_stock.*', 'drug_template.*') // Select necessary columns
                ->get();

            // return $pharmacy_stock;
            // Return the fetched data as a JSON response
            return response()->json([
                'result' => true,
                'data' => $pharmacy_stock
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors and return a JSON response with the error message
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching the Pharmacy Stock.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getStockByAvailability(Request $request, $availability, $storeId)
    {

        try {
            // Determine the filtering conditions based on availability
            switch ($availability) {
                case '0':  // Available
                    $pharmacy_stock = PharmacyStock::join('drug_template', 'pharmacy_stock.drug_template_id', '=', 'drug_template.drug_template_id')
                        ->where('pharmacy_stock.ohc_pharmacy_id', $storeId) // Filter by store ID
                        ->where('pharmacy_stock.availability', $availability)
                        ->select('pharmacy_stock.*', 'drug_template.*')
                        ->get();
                    break;

                case '1':  // Expired
                    $pharmacy_stock = PharmacyStock::join('drug_template', 'pharmacy_stock.drug_template_id', '=', 'drug_template.drug_template_id')
                    ->where('pharmacy_stock.ohc_pharmacy_id', $storeId)
                    ->whereDate('pharmacy_stock.expiry_date', '<', now()) // Check for expiry before today
                        ->select('pharmacy_stock.*', 'drug_template.*')
                        ->get();
                    break;

                case '2':  // Sold (Stock = 0)
                    $pharmacy_stock = PharmacyStock::join('drug_template', 'pharmacy_stock.drug_template_id', '=', 'drug_template.drug_template_id')
                    ->where('pharmacy_stock.ohc_pharmacy_id', $storeId)
                    ->where('pharmacy_stock.current_availability', '=', 0) // Filter for sold stock
                        ->select('pharmacy_stock.*', 'drug_template.*')
                        ->get();
                    break;

                case '3':  // Stop Issuing (Stock within 45 days of expiry)
                    $pharmacy_stock = PharmacyStock::join('drug_template', 'pharmacy_stock.drug_template_id', '=', 'drug_template.drug_template_id')
                        ->where('pharmacy_stock.ohc_pharmacy_id', $storeId) // Filter by store ID
                        ->whereDate('pharmacy_stock.expiry_date', '>=', now()) // Make sure the expiry date is after today
                        ->whereDate('pharmacy_stock.expiry_date', '<=', now()->addDays(45)) // Expiry date is within the next 45 days
                        ->select('pharmacy_stock.*', 'drug_template.*')
                        ->get();

                    break;

                default:
                    return response()->json([
                        'result' => false,
                        'message' => 'Invalid availability type'
                    ]);
            }

            return response()->json([
                'result' => true,
                'data' => $pharmacy_stock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Error in fetching pharmacy stock data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
