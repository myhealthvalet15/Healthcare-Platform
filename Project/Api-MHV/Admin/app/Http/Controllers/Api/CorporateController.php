<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MasterCorporate;
use App\Models\MasterCorporateUser;
use App\Models\MasterCorporateAddress;
use App\Models\Address;
use App\Models\MasterCorporateFinancial;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CorporateController extends Controller
{

    public function addCorporate(Request $request)
    {
        // Log the incoming request data
        // Log::info("addCorporate - Incoming request data:", $request->all());

        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'corporate_id' => 'required',
                'location_id' => 'required',
                'corporate_no' => 'nullable|string',
                'corporate_name' => 'required|string',
                'display_name' => 'required|string',
                'registration_no' => 'nullable|string',
                'industry' => 'nullable|string',
                'industry_segment' => 'nullable|string',
                'prof_image' => 'nullable',  // Image validation
                'company_profile' => 'nullable|string',
                'created_on' => 'nullable|date',
                'gstin' => 'nullable',
                'discount' => 'nullable',
                'valid_from' => 'nullable|date',
                'valid_upto' => 'nullable|date',
                'corporate_color' => 'nullable',
                'color' => 'nullable|string',
                'active_status' => 'nullable|boolean',
            ]);

            // Handle validation failure
            if ($validator->fails()) {
                Log::warning("addCorporate - Validation failed:", $validator->errors()->toArray());
                return response()->json([
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Handle file upload
            //    $prof_image = null;
            //    if ($request->hasFile('prof_image')) {
            //        // Get the uploaded file
            //        $file = $request->file('prof_image');

            //        // Log file details before storing
            //     //    // Log::info('Profile image upload initiated:', [
            //     //        'file_name' => $file->getClientOriginalName(),
            //     //        'file_size' => $file->getSize(),
            //     //        'file_mime_type' => $file->getMimeType(),
            //     //    ]);

            //        // Store the file in the public/profiles directory
            //        $prof_image = $file->store('public/profiles');

            //        // Log the file storage path
            //        // Log::info('Profile image stored at:', [
            //            'file_path' => $prof_image
            //        ]);
            //    }

            // Create corporate record
            $corporate = MasterCorporate::create([
                'corporate_id' => $request->corporate_id,
                'location_id' => $request->location_id,
                'corporate_no' => $request->corporate_no,
                'corporate_name' => $request->corporate_name,
                'display_name' => $request->display_name,
                'registration_no' => $request->registration_no,
                'industry' => $request->industry,
                'industry_segment' => $request->industry_segment,
                'prof_image' => $request->prof_image,
                'company_profile' => $request->company_profile,
                'created_by' => $request->created_by,
                'created_on' => now(),
                'gst' => $request->gst,
                'discount' => $request->discount,
                'valid_from' => $request->valid_from,
                'valid_upto' => $request->valid_upto,
                'corporate_color' => $request->corporate_color,
                'active_status' => $request->active_status ?? 1,
            ]);

            // Get the image URL if the profile image was uploaded
            $imageUrl = $prof_image ? Storage::url($prof_image) : null;

            // Log the successful creation of corporate record
            // Log::info('Corporate successfully registered', ['corporate_id' => $corporate->corporate_id]);

            // Return response with corporate data and image URL
            return response()->json([
                'message' => 'Corporate registered successfully!',
                'data' => [
                    'corporate' => $corporate,
                    'prof_image_url' => $imageUrl,  // Include image URL
                ],
            ], 201);
        } catch (QueryException $e) {
            // Log database error
            Log::error('Database error:', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Database error: ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Log general exception
            Log::error('An error occurred:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function editcorporate(Request $request, $id)
    {

        $validator = \Validator::make(['id' => $id], [
            'id' => 'required|string|exists:master_corporate,id',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $corporate = MasterCorporate::where('id', $id)->firstOrFail();


            return response()->json([
                'corporate' => $corporate,
            ], 200);
        } catch (ModelNotFoundException $e) {


            return response()->json([
                'success' => false,
                'message' => 'Corporate record not found.',
            ], 404);
        } catch (\Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updatecorporate(Request $request, $id)
    {
        // Log::info('updatecorporate', $request->all());

        try {
            // Validation rules
            $validator = Validator::make($request->all(), [
                'corporate_no' => 'nullable',
                'corporate_name' => 'required',
                'display_name' => 'required',
                'registration_no' => 'nullable',
                'industry' => 'nullable',
                'industry_segment' => 'nullable',
                'prof_image' => 'nullable',
                'company_profile' => 'nullable',
                'gstin' => 'nullable',
                'discount' => 'nullable',
                'valid_from' => 'required',
                'valid_upto' => 'required',
                'corporate_color' => 'nullable',
                'active_status' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Find corporate record
            $corporate = MasterCorporate::find($id);

            if (!$corporate) {
                return response()->json([
                    'error' => 'Corporate not found!',
                ], 404);
            }

            // Handle profile image upload if provided
            //         if ($request->hasFile('prof_image')) {
            //             $image = $request->file('prof_image');

            // // Generate a unique file name with the original file extension
            // $randomFileName = uniqid('prof_', true) . '.' . $image->getClientOriginalExtension();

            // // Define the storage path for the image
            // $storagePath = 'profiles/' . $randomFileName;

            // // Store the uploaded file in the specified path
            // $profImagePath = $image->storeAs('public/' . $storagePath);
            // // Save the stored path in the database
            // if($profImagePath){
            // $corporate->prof_image = $storagePath;
            //         }
            // }

            // Update corporate record with provided data
            $corporate->update([
                'corporate_no' => $request->corporate_no,
                'corporate_name' => $request->corporate_name,
                'display_name' => $request->display_name,
                'registration_no' => $request->registration_no,
                'industry' => $request->industry,
                'industry_segment' => $request->industry_segment,
                'company_profile' => $request->company_profile,
                'gstin' => $request->gstin,
                'prof_image' => $request->prof_image,
                'discount' => $request->discount,
                'valid_from' => $request->valid_from,
                'valid_upto' => $request->valid_upto,
                'corporate_color' => $request->corporate_color,
                'active_status' => $request->active_status,
                'created_by' => $request->created_by ?? 'mhvadmin',
                'created_on' => now(),
            ]);

            // Save the updated prof_image field if it was uploaded
            if (isset($profImagePath)) {
                $corporate->prof_image = $profImagePath;
                $corporate->save();
            }

            // Log success
            // Log::info('Corporate updated successfully', ['corporate' => $corporate]);

            // Return success response
            return response()->json([
                'message' => 'Corporate updated successfully!',
                'data' => $corporate,
            ], 201);
        } catch (QueryException $e) {
            // Handle database errors
            Log::error('Database error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            // Handle other errors
            Log::error('General error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }



    public function addCorporateUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'nullable',
            'gender' => 'nullable',
            'email' => 'required',
            'password' => 'required',
            'mobile_country_code' => 'nullable',
            'mobile_no' => 'nullable',
            'createdby' => 'nullable',
            'isactive' => 'boolean',
            'ispasswordchanged' => 'boolean',
            'super_admin' => 'boolean',
            'signup_by' => 'nullable|string|max:50',
            'signup_role' => 'nullable|string|max:50',
            'signup_type' => 'nullable|string|max:50',
            'signup_on' => 'nullable|date',
            'aadhar' => 'nullable',
            'age' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $user = MasterCorporateUser::create([
            'user_id' => $request->user_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'mobile_country_code' => "+91",
            'mobile_num' => $request->mobile_no,
            'isactive' => 1,
            'aadhar' => $request->aadhar,

        ]);

        return response()->json(['user' => $user], 201);
    }
    public function corporate_index(Request $request)
    {

        try {
            $corporate = MasterCorporate::all();
            $count = $corporate->count();

            return response()->json([
                'status' => 200,
                'message' => 'Data retrieved successfully',
                'count' => $count,
                'data' => $corporate,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function corporate_address(Request $request)
    {
        // Log::info("Attempting to create a corporate address", ['request' => $request->all()]);

        try {
            // Define validation rules
            $rules = [
                'corporate_id' => 'nullable',
                'location_id' => 'nullable',
                'pincode_id' => 'nullable',
                'country_id' => 'required',
                'state_id' => 'nullable',
                'city_id' => 'nullable',
                'latitude' => 'nullable',
                'longitude' => 'nullable',
                'website_link' => 'nullable',
            ];

            // Validate the request
            $validated = $request->validate($rules);

            // Create a corporate address
            $address = MasterCorporateAddress::create($validated);

            // Log success
            // Log::info("Corporate address created successfully", ['address' => $address]);

            return response()->json([
                'message' => 'Address created successfully',
                'data' => $address,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::warning("Validation failed for corporate address", [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Log general errors
            Log::error("Error while creating corporate address", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit_address(Request $request, $id, $corporate_id)
    {
        //dd($corporate_id);
        try {


            $master_corp = MasterCorporate::where('id', $id)
                ->select('corporate_name', 'location_id')
                ->first();

            $corp_address = MasterCorporateAddress::where('corporate_id', $corporate_id)
                ->where('location_id', $master_corp->location_id)
                ->select('id', 'corporate_id', 'location_id', 'latitude', 'longitude', 'website_link', 'pincode_id')
                ->first();


            if (!$corp_address) {
                return response()->json([
                    'message' => 'Corporate address not found',
                ], 404);
            }

            $address = Address::where('address_id', $corp_address->pincode_id)->first();


            $pincode = Address::where('address_id', $corp_address->pincode_id)
                ->select('address_id', 'address_name')->first();
            $area = Address::where('address_id', $address->area_id)
                ->select('address_id', 'address_name')->first();
            $city = Address::where('address_id', $address->city_id)
                ->select('address_id', 'address_name')->first();
            $state = Address::where('address_id', $address->state_id)
                ->select('address_id', 'address_name')->first();
            $country = Address::where('address_id', $address->country_id)
                ->select('address_id', 'address_name')->first();



            if (!$address) {
                return response()->json([
                    'message' => 'Address not found',
                ], 404);
            }

            return response()->json([
                'message' => 'Address fetched successfully',
                'corporate_address' => $corp_address,
                'pincode' => $pincode,
                'area' => $area,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'corporate_name' => $master_corp,
            ], 200);
        } catch (\Exception $e) {
            //log::infoerror("Error while fetching corporate address", ['exception' => $e]);

            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update_corporate_address(Request $request, $id)
    {
        // Log::info($request->all());

        try {
            $rules = [

                'pincode_id' => 'nullable',
                'country_id' => 'nullable',
                'state_id' => 'nullable',
                'city_id' => 'nullable',
                'area_id' => 'nullable',
                'latitude' => 'nullable',
                'longitude' => 'nullable',
                'website_link' => 'nullable',
            ];

            $validated = $request->validate($rules);

            $address = MasterCorporateAddress::findOrFail($id);

            $address->update($validated);

            // Log success
            // // Log::info("Corporate address updated successfully", ['address' => $address]);

            return response()->json([
                'message' => 'Address updated successfully',
                'data' => $address,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::warning("Validation failed for corporate address update", [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Log if the address is not found
            Log::warning("Corporate address not found", [
                'address_id' => $id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Corporate address not found',
            ], 404);
        } catch (\Exception $e) {
            // Log general errors
            Log::error("Error while updating corporate address", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }





    public function corporate_location(Request $request)
    {
        // Log the start and end of the process


        // Log::info("corporate_locations: ", $request->all());
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'required',
            'location_id' => 'nullable',
            'corporate_no' => 'nullable',
            'corporate_name' => 'required|string',
            'display_name' => 'required|string',
            'registration_no' => 'nullable',
            'industry' => 'nullable',
            'industry_segment' => 'nullable',
            'company_profile' => 'nullable',
            'created_on' => 'nullable',
            'gstin' => 'nullable',
            'discount' => 'nullable',
            'valid_from' => 'required|date',
            'valid_upto' => 'required|date',
            'corporate_color' => 'nullable',
            'color' => 'nullable|string',
            'active_status' => 'nullable|boolean',
        ]);

        // Log validation status
        if ($validator->fails()) {
            Log::warning("Validation failed for addCorporate", ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Log before database insertion
        // Log::info("Validation passed. Preparing to insert corporate record.");
        // $locationId = $this->uniquelocation_ids($request);

        // Create corporate record
        $corporate = MasterCorporate::create([
            'corporate_id' => $request->corporate_id,
            'location_id' => $request->location_id,
            'corporate_no' => $request->corporate_no,
            'corporate_name' => $request->corporate_name,
            'display_name' => $request->display_name,
            'registration_no' => $request->registration_no,
            'industry' => $request->industry,
            'industry_segment' => $request->industry_segment,
            'company_profile' => $request->company_profile,
            'created_by' => $request->created_by ?? 'system',
            'created_on' => now(),
            'gstin' => $request->gstin,
            'discount' => $request->discount ?? '1235',
            'valid_from' => $request->valid_from,
            'valid_upto' => $request->valid_upto,
            'corporate_color' => $request->corporate_color,
            'active_status' => $request->active_status ?? '1',
        ]);

        // Log successful database insertion
        // Log::info("Corporate successfully registered", ['corporate_id' => $corporate->corporate_id]);

        // Return response
        return response()->json([
            'message' => 'Corporate registered successfully!',
            'data' => $corporate,
        ], 201);
    }

    public function address_location(Request $request, $id)
    {
        // Log the start and end of the process
        // Log::info("start_corporate_id");
        // Log::info("corporate_id: " . $id);
        // Log::info("corporate_locations: ", $request->all());

        try {
            // Validate the input
            $validator = Validator::make($request->all(), [
                'location_id' => 'required',
            ]);

            // If validation fails, return a JSON response with errors
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Fetch the address record and update it
            $address = MasterCorporateAddress::where('corporate_id', $id)->first(); // Use `first` to get a single record

            // Check if the address exists
            if (!$address) {
                return response()->json(['message' => 'Address not found'], 404);
            }

            // Update the location_id
            $address->location_id = $request->location_id;

            // Save the updated address record
            $address->save();

            // Log successful update
            // Log::info("Updated location_id for corporate_id: " . $id);

            return response()->json(['message' => 'Location updated successfully']);
        } catch (\Exception $e) {
            // Log error and return a response
            Log::error("Error updating location: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function generateUniquecorpId(Request $request)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 10;  // 8 characters after 'MC' (making a total of 10)

        // Generate the user ID starting with 'MC'
        $userId = 'MC';
        for ($i = 0; $i < $length; $i++) {
            $userId .= $characters[random_int(0, strlen($characters) - 1)];
        }
        //  $userIds = 'MCBoAmzVFigh';
        // Check if the generated user ID already exists in the database
        $existingUser = MasterCorporate::where('corporate_id', $userIds)
            ->exists();


        // Retry up to 5 times if the user ID already exists
        $retries = 0;
        while ($existingUser && $retries < 5) {
            $userId = 'MC';  // Reset user ID to start with 'MC'
            for ($i = 0; $i < $length; $i++) {
                $userId .= $characters[random_int(0, strlen($characters) - 1)];
            }

            // Check again if the ID exists
            $existingUser = MasterCorporate::where('corporate_id', $userId)->exists();
            $retries++;
        }

        // If it's still not unique after retries, throw an error
        if ($existingUser) {
            throw new \Exception('Unable to generate a unique corporate ID after multiple attempts.');
        }

        return $userId;
    }
    public function uniquelocation_ids(Request $request)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 10;
        $prefix = 'MC';

        $retries = 0;
        $maxRetries = 50;

        do {
            $userId = $prefix;
            for ($i = 0; $i < $length; $i++) {
                $userId .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $existingCorporate = MasterCorporate::where('corporate_id', $userId)->exists();
            $existingLocation = MasterCorporate::where('location_id', $userId)->exists();

            $isUnique = !$existingCorporate && !$existingLocation;

            $retries++;
        } while (!$isUnique && $retries < $maxRetries);

        if (!$isUnique) {
            throw new \Exception('Unable to generate a unique corporate ID after multiple attempts.');
        }

        return response()->json([
            'message' => 'Corporate registered successfully!',
            'data' => $userId,
        ], 201);
    }

    public function maincorporate(Request $request)
    {
        try {
            $corporate = MasterCorporate::query()
                ->whereColumn('corporate_id', 'location_id')
                ->select(['id', 'corporate_id', 'corporate_name'])
                ->get();
            return $corporate->isEmpty()
                ? response()->json(['success' => false, 'data' => []], 200)
                : response()->json(['success' => true, 'data' => $corporate], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while fetching data.'], 500);
        }
    }

    public function getCorporateLocations(Request $request, $corporate_id)
    {
        try {
            if (empty($corporate_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Corporate ID is required.'
                ], 400);
            }

            $corporates = MasterCorporate::query()
                ->where('corporate_id', $corporate_id)
                ->select(['id', 'location_id', 'display_name'])
                ->get();

            if ($corporates->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sub-location data found.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $corporates
            ], 200);
        } catch (\Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching corporate location data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
