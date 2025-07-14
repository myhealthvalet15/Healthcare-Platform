<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Address;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Mhvadmin;
use Exception;

class AddressController extends Controller
{
    public function show()
    {
        dd('hello');
    }
    public function countryIndex(Request $request)
    {
        try {
            $addresses = Address::where('address_type', 1)
                ->get(['address_name']);

            return response()->json(['addresses' => $addresses], 200);
        } catch (Exception $e) {
            Log::error('Error fetching countries: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch countries. Please try again later.'], 500);
        }
    }

    public function countryCreate(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_type' => 'required',
                'active_status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $address = Address::create([
                'address_name' => $request->input('address_name'),
                'address_type' => $request->input('address_type'),
                'active_status' => $request->input('active_status'),

            ]);

            return response()->json(['message' => 'Address added successfully!', 'address' => $address], 200);
        } catch (Exception $e) {
            Log::error('Error creating country: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add address. Please try again later.'], 500);
        }
    }

    public function stateIndex()
    {
        try {
            $countries = Address::where('address_type', 'country')
                ->get(['address_id', 'address_name']);
            $states = Address::where('address_type', 'state')
                ->get(['address_name']);

            return response()->json(['countries' => $countries, 'states' => $states], 200);
        } catch (Exception $e) {
            Log::error('Error fetching states: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch states. Please try again later.'], 500);
        }
    }

    public function stateAdd(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_id' => 'required',
                'address_type' => 'required',
                'active_status' => 'required'

            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $address = Address::create([
                'address_name' => $request->input('address_name'),
                'address_type' => $request->input('address_type'),
                'country_id' => $request->input('address_id'),
                'active_status' => $request->input('active_status'),
            ]);


            return response()->json(['message' => 'State added successfully!', 'address' => $address], 200);
        } catch (Exception $e) {
            Log::error('Error adding state: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add state. Please try again later.'], 500);
        }
    }

    public function cityIndex()
    {
        try {
            $states = Address::where('address_type', 'state')
                ->get(['address_id', 'address_name', 'country_id']);

            return response()->json(['states' => $states], 200);
        } catch (Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch cities. Please try again later.'], 500);
        }
    }

    public function countryFind(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [

                'address_id' => 'required'


            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $addressId = $request->input('address_id');
            $countryId = Address::where('address_id', $addressId)->value('country_id');


            $addresses = Address::where('address_id', $countryId)->get(['address_name', 'address_id']);

            return response()->json(['message' => 'Addresses fetched successfully.', 'addresses' => $addresses], 200);
        } catch (Exception $e) {
            Log::error('Error finding country: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch addresses. Please try again later.'], 500);
        }
    }

    public function cityAdd(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_type' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'active_status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $address = Address::create([
                'address_name' => $request->input('address_name'),
                'address_type' => $request->input('address_type'),
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'active_status' => $request->input('active_status')

            ]);

            return response()->json(['message' => 'City added successfully!', 'address' => $address], 200);
        } catch (Exception $e) {
            Log::error('Error adding city: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add city. Please try again later.'], 500);
        }
    }

    public function areaIndex(Request $request)
    {
        try {
            $areas = Address::where('address_type', 'city')
                ->get(['address_id', 'address_name', 'country_id', 'state_id']);

            return response()->json(['areas' => $areas], 200);
        } catch (Exception $e) {
            Log::error('Error fetching areas: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch areas. Please try again later.'], 500);
        }
    }

    public function countrystate_find(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required',

            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $addressId = $request->input('address_id');
            $address = Address::where('address_id', $addressId)->first(['country_id', 'state_id']);

            if ($address) {
                $countryId = $address->country_id;
                $stateId = $address->state_id;

                $countryInfo = Address::where('address_id', $countryId)->get(['address_name', 'address_id']);
                $stateInfo = Address::where('address_id', $stateId)->get(['address_name', 'address_id']);

                return response()->json(['message' => 'Addresses fetched successfully.', 'country' => $countryInfo, 'state' => $stateInfo], 200);
            }

            return response()->json(['message' => 'Address not found.'], 404);
        } catch (Exception $e) {
            Log::error('Error finding country and state: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch addresses. Please try again later.'], 500);
        }
    }

    public function areaAdd(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_type' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'active_status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $address = Address::create([
                'address_name' => $request->input('address_name'),
                'address_type' => $request->input('address_type'),
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'active_status' => $request->input('active_status'),

            ]);

            return response()->json(['message' => 'Area added successfully!', 'address' => $address], 200);
        } catch (Exception $e) {
            Log::error('Error adding area: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add area. Please try again later.'], 500);
        }
    }

    public function pincodeIndex()
    {
        try {

            $pincodeindex = Address::query()
                ->where('address_type', 'pincode')
                ->select('address_id', 'address_name')
                ->get();

            return response()->json([
                'pincodeindex' => $pincodeindex
            ], 200);
        } catch (Exception $e) {
            Log::error('Error fetching pincodes: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch pincodes. Please try again later.'], 500);
        }
    }

    public function countrystatecity_find(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required',

            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $addressId = $request->input('address_id');
            $address = Address::where('address_id', $addressId)->first(['country_id', 'state_id', 'city_id']);

            if ($address) {
                $countryId = $address->country_id;
                $stateId = $address->state_id;
                $cityId = $address->city_id;

                $countryInfo = Address::where('address_id', $countryId)->get(['address_name', 'address_id']);
                $stateInfo = Address::where('address_id', $stateId)->get(['address_name', 'address_id']);
                $cityInfo = Address::where('address_id', $cityId)->get(['address_name', 'address_id']);

                return response()->json([
                    'message' => 'Addresses fetched successfully.',
                    'country' => $countryInfo,
                    'state' => $stateInfo,
                    'city' => $cityInfo
                ], 200);
            }

            return response()->json(['message' => 'Address not found.'], 404);
        } catch (Exception $e) {
            Log::error('Error finding country, state, and city: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch addresses. Please try again later.'], 500);
        }
    }

    public function pincodeAdd(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_type' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'area_id' => 'required',
                'active_status' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $address = Address::create([
                'address_name' => $request->input('address_name'),
                'address_type' => $request->input('address_type'),
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'area_id' => $request->input('area_id'),
                'active_status' => $request->input('active_status'),
            ]);

            return response()->json(['message' => 'Pincode added successfully!', 'address' => $address], 200);
        } catch (Exception $e) {
            Log::error('Error adding pincode: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add pincode. Please try again later.'], 500);
        }
    }

    public function countrystatecityarea_find(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_id' => 'required',

            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $addressId = $request->input('address_id');
            $address = Address::where('address_id', $addressId)->first(['country_id', 'state_id', 'city_id']);

            if ($address) {
                $countryId = $address->country_id;
                $stateId = $address->state_id;
                $cityId = $address->city_id;
                $areaId = $address->area_id;


                $countryInfo = Address::where('address_id', $countryId)->get(['address_name', 'address_id']);
                $stateInfo = Address::where('address_id', $stateId)->get(['address_name', 'address_id']);
                $cityInfo = Address::where('address_id', $cityId)->get(['address_name', 'address_id']);
                //  $areaInfo = Address::where('address_id', $areaId)->get(['address_name', 'address_id']);

                return response()->json([
                    'message' => 'Addresses fetched successfully.',
                    'country' => $countryInfo,
                    'state' => $stateInfo,
                    'city' => $cityInfo,

                ], 200);
            }

            return response()->json(['message' => 'Address not found.'], 404);
        } catch (Exception $e) {
            Log::error('Error finding country, state, and city: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch addresses. Please try again later.'], 500);
        }
    }



    public function location_index(Request $request)
    {
        try {

            $addresses = Address::whereIn('address_type', ['pincode', 'area', 'city', 'state', 'country'])
                ->select('address_id', 'address_name', 'address_type')
                ->get();

            $groupedAddresses = $addresses->groupBy('address_type');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'pincode' => $groupedAddresses->get('pincode', []),
                    'area' => $groupedAddresses->get('area', []),
                    'city' => $groupedAddresses->get('city', []),
                    'state' => $groupedAddresses->get('state', []),
                    'country' => $groupedAddresses->get('country', []),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving location data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function area_find(Request $request)
    {
        $request->validate([
            'address_name' => 'required',
        ]);
        $address_name = $request->input('address_name');
        $addresses = Address::where('address_name', $address_name)->get();

        if ($addresses->isEmpty()) {
            return response()->json(['message' => 'No addresses found'], 404);
        }
        $areas = Address::whereIn('address_id', $addresses->pluck('area_id'))
            ->select('address_id', 'address_name', 'address_type')
            ->get();
        return response()->json($areas);
    }
    public function pincodefind(Request $request)
    {
        // Log the request data for debugging
        // Log::info($request->all());

        // Validate the input: 'address_name' must be provided
        $validator = Validator::make($request->all(), [
            'address_name' => 'required|min:4',  // Enforce minimum length for better performance
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $addressName = $request->input('address_name');

        $cacheKey = "address_{$addressName}";

        $address = Cache::remember($cacheKey, 60, function () use ($addressName) {
            return Address::where('address_name', 'LIKE', "%$addressName%")
                ->select('address_id', 'address_name')
                ->limit(10)
                ->get();
        });

        if ($address->isEmpty()) {
            return response()->json(['message' => 'Address not found.'], 404);
        }

        // Return the found addresses and the count
        return response()->json([
            'count' => $address->count(), // Count of the found addresses
            'addresses' => $address,      // List of found addresses
        ], 200);
    }
    public function findAdminByToken(Request $request)
    {
        try {
            $authorizationHeader = $request->header('Authorization');
            if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
                return response()->json(['error' => 'Token not provided or invalid'], 400);
            }
            $token = str_replace('Bearer ', '', $authorizationHeader);
            dd($token);
            $accessToken = \Laravel\Passport\Token::where('id', $token)->first();
            dd($accessToken);
            exit();
            if (!$accessToken || $accessToken->revoked) {
                return response()->json(['error' => 'Invalid or revoked token'], 401);
            }
            $admin = Mhvadmin::find($accessToken->user_id);
            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }
            return response()->json([
                'email' => $admin->email,
                'admin_name' => $admin->admin_name,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error finding admin by token: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
