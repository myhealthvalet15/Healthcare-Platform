<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CorporateRequest;


class LocationController extends Controller
{
    protected $httpClient;
    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    public function corporatelocation(Request $request, $id, $corporate_id)
    {
        try {
            $accessToken = $request->cookie('access_token');
            $headers = ['Authorization' => 'Bearer ' . $accessToken];

            $response = $this->makeApiRequest('GET', "/api/corporate/editcorporate/{$id}", $headers);
            $addressResponse = $this->makeApiRequest('GET', "/api/corporate/edit_address/{$id}/{$corporate_id}", $headers);
            $corpAdminResponse = $this->makeApiRequest('GET', "/api/Corporate_admin_user/show/{$id}/{$corporate_id}", $headers);
            $locationResponse = $this->makeApiRequest('GET', "/api/uniquelocation_ids", $headers);

            if (!$response['success'] || !$addressResponse['success'] || !$locationResponse['success']) {
                return redirect()->back()->withErrors(['error' => 'Failed to fetch required data.']);
            }

            $corporate = $response['data']['corporate'] ?? [];
            $locationId = $locationResponse['data'] ?? [];
            $emptype = $corpAdminResponse['data']['user'] ?? [];
            $addressData = $addressResponse['data'] ?? [];

            $corporateName = $addressData['corporate_name'] ?? '';
            $corporateAddress = $addressData['corporate_address'] ?? '';
            $pincode = $addressData['pincode'] ?? '';
            $area = $addressData['area'] ?? '';
            $city = $addressData['city'] ?? '';
            $state = $addressData['state'] ?? '';
            $country = $addressData['country'] ?? '';

            return view('content.corporate_list.corporate.corporate_location', compact(
                'corporate',
                'locationId',
                'corporateName',
                'corporateAddress',
                'pincode',
                'area',
                'city',
                'state',
                'country',
                'emptype'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


    private function makeApiRequest($method, $uri, $headers)
    {
        try {
            $response = $this->httpClient->request($method, $uri, ['headers' => $headers]);
            return $response;
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function addCorporate(Request $request)
    {
        // Log::info("Incoming request for adding corporate", $request->all());

        $rules = [
            'accountDetails.corporate_name' => 'required|string',
            // 'accountDetails.corporate_no' => 'required|string',
            'accountDetails.corporate_id' => 'required|string',
            'accountDetails.location_id' => 'required|string',

            'accountDetails.display_name' => 'required|string',
            // 'accountDetails.registration_no' => 'nullable|string',
            // 'accountDetails.company_profile' => 'nullable|string',
            // 'accountDetails.prof_image' => 'nullable',
            // 'accountDetails.industry' => 'nullable|string',
            // 'accountDetails.gstin' => 'nullable|string',
            // 'accountDetails.discount' => 'nullable',
            'accountDetails.valid_from' => 'required|date',
            'accountDetails.valid_upto' => 'required|date',
            // 'accountDetails.corporate_color' => 'nullable|string',
            // 'accountDetails.active_status' => 'nullable|boolean',
            // 'address.pincode' => 'nullable',
            // 'address.area' => 'nullable',
            // 'address.city' => 'nullable',
            // 'address.state' => 'nullable',
            // 'address.country' => 'nullable',
            // 'address.latitude' =>'nullable',
            // 'address.longitude' => 'nullable',
            // 'address.website_link' => 'nullable',
            'corporateAdminUser.corporate_admin_user_id' => 'required',
            'corporateAdminUser.first_name' => 'required|string',
            'corporateAdminUser.last_name' => 'required|string',
            // 'corporateAdminUser.dob' => 'nullable',
            // 'corporateAdminUser.gender' =>'nullable',
            'corporateAdminUser.email' => 'required|email',
            'corporateAdminUser.password' => 'required|string|min:8',
            // 'corporateAdminUser.mobile_country_code' => 'nullable',
            // 'corporateAdminUser.mobile_num' => 'nullable',
            'corporateAdminUser.aadhar' => 'nullable',
            // 'corporateAdminUser.age' => 'nullable',
            // 'corporateAdminUser.active_status' => 'nullable',
            // 'corporateAdminUser.super_admin' => 'nullable',

        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            // Log::info($validator->errors());
            // Log::error('Validation error: ' . $validator->errors()->toJson());
            return response()->json(['error' => $validator->errors()], 422);
        }
        try {
            $data = [
                [
                    'name' => 'corporate_id',
                    'contents' => $request->input('accountDetails.corporate_id'),
                ],
                [
                    'name' => 'location_id',
                    'contents' => $request->input('accountDetails.location_id'),
                ],
                [
                    'name' => 'corporate_name',
                    'contents' => $request->input('accountDetails.corporate_name'),
                ],
                [
                    'name' => 'display_name',
                    'contents' => $request->input('accountDetails.display_name'),
                ],
                [
                    'name' => 'corporate_no',
                    'contents' => $request->input('accountDetails.corporate_no'),
                ],
                [
                    'name' => 'registration_no',
                    'contents' => $request->input('accountDetails.registration_no'),
                ],
                [
                    'name' => 'industry',
                    'contents' => $request->input('accountDetails.industry'),
                ],
                [
                    'name' => 'industry_segment',
                    'contents' => $request->input('accountDetails.industry_segment'),
                ],
                [
                    'name' => 'company_profile',
                    'contents' => $request->input('accountDetails.company_profile') ?? '',
                ],
                [
                    'name' => 'created_by',
                    'contents' => $request->input('accountDetails.created_by'),
                ],
                [
                    'name' => 'valid_from',
                    'contents' => $request->input('accountDetails.valid_from'),
                ],
                [
                    'name' => 'valid_upto',
                    'contents' => $request->input('accountDetails.valid_upto'),
                ],
                [
                    'name' => 'corporate_color',
                    'contents' => $request->input('accountDetails.corporate_color'),
                ],
                [
                    'name' => 'gstin',
                    'contents' => $request->input('accountDetails.gstin'),
                ],
                [
                    'name' => 'discount',
                    'contents' => $request->input('accountDetails.discount'),
                ],
            ];


            if ($request->hasFile('prof_image')) {
                $image = $request->file('prof_image');
                $data[] = [
                    'name' => 'prof_image',
                    'contents' => fopen($image->getPathname(), 'r'),
                    'filename' => $image->getClientOriginalName(),
                ];
            }

            // Log::info('Sending data to external API:', $data);

            $response1 = $this->httpClient->request('POST', '/api/corporate/add-corporate', [
                'multipart' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            // Log::info('$response1',$response1);

            $addressData = $this->buildAddressData($request);

            $response2 = $this->httpClient->request('POST', '/api/corporate/corporate_address', [
                'json' => $addressData,
                'headers' => $this->buildHeaders($request),
            ]);

            // Log::info('$response2',$response2);



            $corporateAdminUserData = $this->buildCorporateAdminUserData($request);
            // Log::info('Sending corporate admin user data to external API:', $corporateAdminUserData);
            $response5 = $this->httpClient->request('POST', 'api/Corporate_admin_user/create', [
                'multipart' => $corporateAdminUserData,
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);




            // Log::info('$response2',$response2);

            // Log::info('$response5',$response5);






            if ($response5['success']) {
                // Return or handle the success message
                return response()->json([
                    'success' => true,
                    'message' => $response5['message'], // The success message from the API response
                    'data' => $response5['data'],      // The data returned by the API
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send corporate data.',
                ], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while sending data to the API:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }



    private function buildAddressData($request)
    {
        return [
            'corporate_id' => $request->input('accountDetails.corporate_id'),
            'location_id' => $request->input('accountDetails.location_id'),
            'pincode_id' => $request->input('address.pincode'),
            'area_id' => $request->input('address.area'),
            'city_id' => $request->input('address.city'),
            'state_id' => $request->input('address.state'),
            'country_id' => $request->input('address.country'),
            'latitude' => $request->input('address.latitude'),
            'longitude' => $request->input('address.longitude'),
            'website_link' => $request->input('address.website_link'),
        ];
    }

    private function buildCorporateAdminUserData($request)
    {
        return [
            ['name' => 'corporate_id', 'contents' => $request->input('accountDetails.corporate_id')],
            ['name' => 'location_id', 'contents' => $request->input('accountDetails.location_id')],
            ['name' => 'corporate_admin_user_id', 'contents' => $request->input('corporateAdminUser.corporate_admin_user_id')],
            ['name' => 'first_name', 'contents' => $request->input('corporateAdminUser.first_name')],
            ['name' => 'last_name', 'contents' => $request->input('corporateAdminUser.last_name')],
            ['name' => 'dob', 'contents' => $request->input('corporateAdminUser.dob')],
            ['name' => 'gender', 'contents' => $request->input('corporateAdminUser.gender')],
            ['name' => 'email', 'contents' => $request->input('corporateAdminUser.email')],
            ['name' => 'password', 'contents' => $request->input('corporateAdminUser.password')],
            ['name' => 'mobile_country_code', 'contents' => $request->input('corporateAdminUser.mobile_country_code')],
            ['name' => 'mobile_num', 'contents' => $request->input('corporateAdminUser.mobile_num')],
            ['name' => 'aadhar', 'contents' => $request->input('corporateAdminUser.aadhar')],
            ['name' => 'age', 'contents' => $request->input('corporateAdminUser.age')],
            ['name' => 'active_status', 'contents' => $request->input('corporateAdminUser.active_status')],
            ['name' => 'super_admin', 'contents' => $request->input('corporateAdminUser.super_admin')],
        ];
    }

    private function buildHeaders($request)
    {
        // Define your headers here (e.g., API keys, Authorization tokens)
        return [
            'Authorization' => 'Bearer ' . $request->header('Authorization'),
            'Content-Type' => 'application/json',
        ];
    }
}
