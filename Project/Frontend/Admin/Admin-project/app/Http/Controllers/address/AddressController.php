<?php

namespace App\Http\Controllers\address;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class AddressController extends Controller
{
    protected $httpClient;

    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    public function area_find(Request $request)
    {
        // Log::info('areaid', $request->all());

        $address_name = $request->address_name;
        // Log::info($request->address_name);

        try {
            $response = $this->httpClient->request('POST', '/api/address/area_find', [
                'form_params' => [
                    'address_name' => $address_name,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);

            // Log::info($response['data']);
            $data = $response['data'];

            if ($response['success']) {
                return response()->json($data);
            } else {
                return response()->json(['error' => 'Request failed'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error('Error in area_find API request: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please try again later.'], 500);
        }
    }


    public function findlocation(Request $request)
    {
        // Log::info('Incoming Request Data:', $request->all());
        // $address_id=4;
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation Failed:', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $apiResponse = $this->httpClient->request('POST', '/api/address/findareastaconcity', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_id' => $request->input('address_id'),
                ],
            ]);

            // Decode response body
            //  $responseBody = json_decode($apiResponse->getBody()->getContents(), true);

            // Log the API response
            // Log::info('API Response:', $apiResponse);

            if (!empty($apiResponse['success']) && $apiResponse['success']) {
                // Extract and log data
                $data = $apiResponse['data'] ?? [];
                // Log::info('Extracted Data:', $data);

                return response()->json([
                    'message' => 'Addresses fetched successfully.',
                    'country' => $data['country'] ?? null,
                    'state' => $data['state'] ?? null,
                    'city' => $data['city'] ?? null,
                ]);
            } else {
                Log::error('API Error Response:', $responseBody);
                return response()->json([
                    'message' => $responseBody['message'] ?? 'Failed to fetch addresses.',
                ], 400);
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Exception occurred while fetching location:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An error occurred while fetching the location. Please try again later.',
            ], 500);
        }
    }
    public function countryindex(Request $request)
    {
        //dd('hello');
        try {
            $response = $this->httpClient->request('POST', '/api/address/country', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            dd($response);
            $data = json_decode($response->getBody()->getContents(), true);
            $address = is_array($data) ? $data : [];

            return view('admin.dashboard.location.country', ['address' => $address]);
        } catch (\Exception $e) {
            Log::error('location country API Error: ' . $e->getMessage());
            return view('admin.dashboard.location.country', [
                'employeeTypes' => [],
                'error' => 'Unable to fetch country. Please try again later.'
            ]);
        }
    }
    public function show()
    {
        // Log::info('hello');
        return 'hello';
    }

    public function countrycreate(Request $request)
    {
        try {
            // Log::info($request->all());

            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_type' => 'required',
                'active_status' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $response = $this->httpClient->request('POST', '/api/address/addcountry', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_name' => $request->input('address_name'),
                    'address_type' => $request->input('address_type'),
                    'active_status' => $request->input('active_status'),
                ],
            ]);


            if ($response->getStatusCode() === 200) {
                return redirect()->route('location.country')->with('success', 'Address added successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to add address. Please try again.');
            }
        } catch (Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function stateindex(Request $request)
    {
        try {
            $response = $this->httpClient->request('POST', '/api/address/state', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $address = is_array($data) ? $data : [];

            $addresses = isset($address['countries']) ? $address['countries'] : [];
            $state = isset($address['states']) ? $address['states'] : [];
            if ($response->getStatusCode() === 200) {

                return view('admin.dashboard.location.state', compact('addresses', 'state'));
            }
        } catch (\Exception $e) {
            Log::error('location country API Error: ' . $e->getMessage());
            return view('admin.dashboard.location.state', [
                'employeeTypes' => [],
                'error' => 'Unable to fetch country. Please try again later.'
            ]);
        }
    }
    public function stateadd(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'address_name' => 'required',
                'address_id' => 'required',
                'address_type' => 'required',
                'active_status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $response = $this->httpClient->request('POST', '/api/address/addstate', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
                'json' => [
                    'address_name' => $request->input('address_name'),
                    'address_type' => $request->input('address_type'),
                    'address_id' => $request->input('address_id'),
                    'active_status' => $request->input('active_status'),
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                return response()->json(['success' => 'State added successfully!']);
            } else {
                return response()->json(['error' => 'Failed to add state. Please try again.'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error('location country API Error: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function cityindex(Request $request)
    {
        try {
            $response = $this->httpClient->request('POST', '/api/address/city', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            if ($response->getStatusCode() === 200) {

                $data = json_decode($response->getBody()->getContents(), true);
                $addresses = is_array($data) ? $data : [];


                return view('admin.dashboard.location.city', compact('addresses'));
            } else {
                return redirect()->back()->with('error', 'Failed to add city. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('location city API Error: ' . $e->getMessage());
            return view('admin.dashboard.location.city', [
                'employeeTypes' => [],
                'error' => 'Unable to fetch city. Please try again later.'
            ]);
        }
    }
    public function countryfind(Request $request)
    {
        try {
            // Log::info($request->all());

            $validator = Validator::make($request->all(), [
                'address_id' => 'required',

            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $responsed = $this->httpClient->request('POST', '/api/address/findcountry', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_id' => $request->input('address_id'),

                ],
            ]);
            $data = json_decode($responsed->getBody()->getContents(), true);
            $addresses = is_array($data) ? $data : [];
            $addressed = $addresses['addresses'];
            $response = [
                'message' => 'Addresses fetched successfully.',
                'addresses' => $addressed,
            ];





            if ($responsed->getStatusCode() === 200) {
                return response()->json($response);
            } else {
                return redirect()->back()->with('error', 'Failed to add address. Please try again.');
            }
        } catch (Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function cityadd(Request $request)
    {
        try {
            $request->validate([
                'address_name' => 'required|string|max:255',
                'address_type' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'active_status' => 'required',
            ]);
            $response = $this->httpClient->request('POST', '/api/address/addcity', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_name' => $request->input('address_name'),
                    'address_type' => $request->input('address_type'),
                    'country_id' => $request->input('country_id'),
                    'state_id' => $request->input('state_id'),

                    'active_status' => $request->input('active_status'),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            if ($response->getStatusCode() === 200) {
                return response()->json(['success' => 'city added successfully!']);
            } else {
                return response()->json(['error' => 'Failed to add state. Please try again.'], $response->getStatusCode());
            }
        } catch (\Exception $e) {

            Log::error('Error creating city: ' . $e->getMessage());


            return response()->json(['error' => 'Failed to create city.'], 500);
        }
    }
    public function areaindex(Request $request)
    {
        try {
            $response = $this->httpClient->request('POST', '/api/address/area', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            if ($response->getStatusCode() === 200) {

                $data = json_decode($response->getBody()->getContents(), true);
                $addresses = is_array($data) ? $data : [];


                return view('admin.dashboard.location.area', compact('addresses'));
            } else {
                return redirect()->back()->with('error', 'Failed to add city. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('location city API Error: ' . $e->getMessage());
            return view('admin.dashboard.location.area', [
                'employeeTypes' => [],
                'error' => 'Unable to fetch city. Please try again later.'
            ]);
        }
    }
    public function countrystate_find(Request $request)
    {

        try {
            // Log::info($request->all());

            $validator = Validator::make($request->all(), [
                'address_id' => 'required',

            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $responsed = $this->httpClient->request('POST', '/api/address/findstacountry', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_id' => $request->input('address_id'),

                ],
            ]);
            $data = json_decode($responsed->getBody()->getContents(), true);
            $addresses = is_array($data) ? $data : [];
            $addressed = $addresses['country'];
            $state = $addresses['state'];

            $response = [
                'message' => 'Addresses fetched successfully.',
                'country' => $addressed,
                'stateinfo' => $state
            ];





            if ($responsed->getStatusCode() === 200) {
                return response()->json($response);
            } else {
                return redirect()->back()->with('error', 'Failed to add address. Please try again.');
            }
        } catch (Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function areaadd(Request $request)
    {

        try {



            $request->validate([
                'address_name' => 'required|string|max:255',
                'address_type' => 'required',
                'country_id' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'active_status' => 'required',
            ]);
            $response = $this->httpClient->request('POST', '/api/address/areaadd', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_name' => $request->input('address_name'),
                    'address_type' => $request->input('address_type'),
                    'country_id' => $request->input('country_id'),
                    'state_id' => $request->input('state_id'),
                    'city_id' => $request->input('city_id'),
                    'active_status' => $request->input('active_status'),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($response->getStatusCode() === 200) {

                return response()->json(['success' => 'area added successfully!']);
            } else {
                return response()->json(['error' => 'Failed to add area. Please try again.'], $response->getStatusCode());
            }
        } catch (\Exception $e) {

            Log::error('Error creating injury: ' . $e->getMessage());


            return response()->json(['error' => 'Failed to create area.'], 500);
        }
    }
    public function pincodeindex(Request $request)
    {
        try {
            $response = $this->httpClient->request('POST', '/api/address/pincode', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            if ($response->getStatusCode() === 200) {

                $data = json_decode($response->getBody()->getContents(), true);
                $addresses = is_array($data) ? $data : [];


                return view('admin.dashboard.location.pincode', compact('addresses'));
            } else {
                return redirect()->back()->with('error', 'Failed to pincode . Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('location city API Error: ' . $e->getMessage());
            return view('admin.dashboard.location.pincode', [
                'employeeTypes' => [],
                'error' => 'Unable to fetch city. Please try again later.'
            ]);
        }
    }
    public function countrystatecity_find(Request $request)
    {

        try {
            // Log::info($request->all());

            $validator = Validator::make($request->all(), [
                'address_id' => 'required',

            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $responsed = $this->httpClient->request('POST', '/api/address/findstaconcity', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_id' => $request->input('address_id'),

                ],
            ]);
            $data = json_decode($responsed->getBody()->getContents(), true);
            $addresses = is_array($data) ? $data : [];
            $addressed = $addresses['country'];
            $state = $addresses['state'];
            $city = $addresses['city'];
            $response = [
                'message' => 'Addresses fetched successfully.',
                'country' => $addressed,
                'state' => $state,
                'city' => $city,
            ];





            if ($responsed->getStatusCode() === 200) {
                return response()->json($response);
            } else {
                return redirect()->back()->with('error', 'Failed to add address. Please try again.');
            }
        } catch (Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function countrystatecityarea_find(Request $request)
    {

        try {
            // Log::info($request->all());

            $validator = Validator::make($request->all(), [
                'address_id' => 'required',

            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $responsed = $this->httpClient->request('POST', '/api/address/findareastaconcity', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_id' => $request->input('address_id'),

                ],
            ]);
            $data = json_decode($responsed->getBody()->getContents(), true);
            // Log::info("area", $data);
            $addresses = is_array($data) ? $data : [];
            $addressed = $addresses['country'];
            $state = $addresses['state'];
            $city = $addresses['city'];
            $area = $addresses['area'];
            $response = [
                'message' => 'Addresses fetched successfully.',
                'country' => $addressed,
                'state' => $state,
                'city' => $city,
                'area' => $area
            ];
            // Log::info("areastate", $response);





            if ($responsed->getStatusCode() === 200) {
                return response()->json($response);
            } else {
                return redirect()->back()->with('error', 'Failed to add address. Please try again.');
            }
        } catch (Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function pincodeadd(Request $request)
    {

        try {
            $request->validate([
                'address_name' => 'required|string|max:255',
                'address_type' => 'required',
                'area_id' => 'required',
                'country_id' => 'required',
                'city_id' => 'required',
                'state_id' => 'required',
                'active_status' => 'required',
            ]);
            $response = $this->httpClient->request('POST', '/api/address/pincodeaadd', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_name' => $request->input('address_name'),
                    'address_type' => $request->input('address_type'),
                    'country_id' => $request->input('country_id'),
                    'state_id' => $request->input('state_id'),
                    'area_id' => $request->input('area_id'),
                    'city_id' => $request->input('city_id'),
                    'active_status' => $request->input('active_status'),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);



            if ($response->getStatusCode() === 200) {

                return response()->json(['success' => 'pincode added successfully!']);
            } else {
                return response()->json(['error' => 'Failed to add pincode. Please try again.'], $response->getStatusCode());
            }
        } catch (\Exception $e) {

            Log::error('Error creating pincode: ' . $e->getMessage());


            return response()->json(['error' => 'Failed to create pincode.'], 500);
        }
    }


    public function findpincode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pincode' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        $pincode = $validatedData['pincode'];
        // Log::info($pincode);

        try {
            $response = $this->httpClient->request('POST', '/api/address/pincode_find', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'address_name' => $pincode,
                ],
            ]);

            // Log::info($response['data']);
            $address = $response['data']['addresses'];
            // Log::info('address',$address);

            return response()->json($address);
        } catch (\Exception $e) {
            Log::error('Error fetching pincode data: ' . $e->getMessage());
            return response()->json(['error' => 'Could not fetch pincode data.'], 500);
        }
    }
}
