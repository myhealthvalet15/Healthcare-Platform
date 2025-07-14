<?php

namespace App\Http\Controllers\form_wizard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;



class Numbered extends Controller
{
  protected $httpClient;
  public function __construct(GuzzleHttpClient $httpClient)
  {
    $this->httpClient = $httpClient;
  }
  public function index(Request $request)
  {
    try {
      $numIds = 1;
      $prefixCode = 'MC';
      $userIds = $this->generateUserIds($prefixCode, $numIds);

      $corporate_user_id = $this->generateCorporateIds($numIds);

     
          
          $userIdsString = implode(',', $userIds);
          $corporateIdsString = implode(',', $corporate_user_id);
          $response2 =  $this->httpClient->request('GET', 'V1/corporate/corporate-components/getAllComponents', [
              'headers' => [
                  'Authorization' => 'Bearer ' . $request->cookie('access_token'),
              ],
          ]);

          if ($response2['success']) {
          $modules = $response2['data']['data'];
          
          return view('content.form-wizard.form-wizard-numbered', compact('modules', 'userIdsString', 'corporateIdsString'));
      } else {
         return redirect()->back()->with('error', 'Failed to pincode . Please try again.');
     }

  } catch (\Exception $e) {
      
     
  }

    
   
  }
  public function generateUserIds($prefixCode, $numIds, $length = 10)
  {

      $userIds = [];
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

      for ($i = 0; $i < $numIds; $i++) {
          $result = '';
          for ($j = 0; $j < $length; $j++) {
              $randomIndex = rand(0, strlen($characters) - 1);
              $result .= $characters[$randomIndex];
          }
          $userIds[] = $prefixCode . $result;
      }

      return $userIds;
  }

  public function generateCorporateIds($numIds, $length = 10)
  {
      $prefixCode = 'MP';

      $userIds = [];
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

      for ($i = 0; $i < $numIds; $i++) {
          $result = '';
          for ($j = 0; $j < $length; $j++) {
              $randomIndex = rand(0, strlen($characters) - 1);
              $result .= $characters[$randomIndex];
          }
          $userIds[] = $prefixCode . $result;
      }

      return $userIds;
  }

   
  public function findlocation(Request $request)
{
    //// Log::info('Request Data:', $request->all());

    // Validate the request input
    $validator = Validator::make($request->all(), [
        'address_id' => 'required|numeric', // Example: ensuring the ID is numeric
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        // Make the API call
        $apiResponse = $this->httpClient->request('POST', '/api/address/findareastaconcity', [
            'headers' => [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'address_id' => $request->input('address_id'),
            ],
        ]);

        // Decode response to array
        //$responseBody = json_decode($apiResponse->getBody()->getContents(), true);

        if (!empty($apiResponse['success']) && $apiResponse['success']) {
            $data = $apiResponse['data'] ?? [];
            return response()->json([
                'message' => 'Addresses fetched successfully.',
                'country' => $data['country'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
            ]);
        } else {
            return response()->json([
                'message' => $apiResponse['message'] ?? 'Failed to fetch addresses.',
            ], 400);
        }
    } catch (\Exception $e) {
        Log::error('Error fetching location:', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'An error occurred while fetching the location. Please try again later.',
        ], 500);
    }
}




public function addCorporate(Request $request)
{
   // Log::info("Incoming request for adding corporate", $request->all());

    // Define the validation rules
    $rules = [
        'accountDetails.corporate_name' => 'nullable|string',
         'accountDetails.corporate_no' => 'required|string',
        'accountDetails.corporate_id' => 'required|string',
        'accountDetails.display_name' => 'required|string',
        // 'accountDetails.registration_no' => 'required|string',
        // 'accountDetails.company_profile' => 'required|string',
        'accountDetails.prof_image' => 'nullable', // Ensure it's an image if it's provided
        // 'accountDetails.industry' => 'required|string',
        // 'accountDetails.gstin' => 'nullable|string',
        // 'accountDetails.discount' => 'required',
        'accountDetails.valid_from' => 'required',
        'accountDetails.valid_upto' => 'required',
        // 'accountDetails.corporate_color' => 'required|string',
        'accountDetails.active_status' => 'required|boolean',
       // 'address.pincode' => 'required|required',
        // 'address.area' => 'required|string',
        // 'address.city' => 'required|string',
        // 'address.state' => 'required|string',
        // 'address.country' => 'required|string',
        // 'address.latitude' => 'required|numeric',
        // 'address.longitude' => 'required|numeric',
        // 'address.website_link' => 'nullable',
        'employeeTypes' => 'required|array',
        // // 'employeeTypes.*' => 'string',
        'corporateAdminUser.first_name' => 'required|string',
        'corporateAdminUser.last_name' => 'required|string',
        // // 'corporateAdminUser.dob' => 'required|date',
        // // 'corporateAdminUser.gender' => 'required|in:male,female,other',
        'corporateAdminUser.email' => 'required|email',
        'corporateAdminUser.password' => 'required|string|min:8',
        // // 'corporateAdminUser.mobile_country_code' => 'required|string',
        // // 'corporateAdminUser.mobile_num' => 'required|string',
        // // 'corporateAdminUser.aadhar' => 'required|string',
        // // 'corporateAdminUser.age' => 'required|integer',
        // 'corporateAdminUser.active_status' => 'required|boolean',
        // 'corporateAdminUser.super_admin' => 'required|boolean',
        'modulesData' => 'required|array',
        // // 'modulesData.*.module_id' => 'required|integer',
        // // 'modulesData.*.sub_module_ids' => 'nullable|array',
        // // 'modulesData.*.sub_module_ids.*' => 'nullable|integer',
    ];

    // Validate the request
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
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
    
        // Handle file upload if present
        if ($request->hasFile('accountDetails.prof_image')) {
            $file = $request->file('accountDetails.prof_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('/profile/prof_image', $filename, 'public');
            
            $multipart[] = [
                'name' => 'prof_image',
                'contents' => $filePath,
               
            ];
        }
    
        // Log request data (be careful with sensitive or large data like files)
        // Log::info('Sending data to external API', [
        //     'fields' => array_map(function ($item) {
        //         return ['name' => $item['name'], 'contents' => $item['contents']];
        //     }, $data)
        // ]);
    
        // Perform the HTTP POST request to external API
        $response1 = $this->httpClient->request('POST', '/api/corporate/add-corporate', [
            'multipart' => $data,
            'headers' => [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ],
        ]);
// Log::info($response1);
        $addressData = $this->buildAddressData($request);

        $response2= $this->httpClient->request('POST', '/api/corporate/corporate_address', [
            'json' => $addressData,
             'headers' => $this->buildHeaders($request),
         ]);
        

         $postData = [
            'employee_type_name' => $request->input('employeeTypes'),
            'corporate_id' => $request->input('address.corporate_id'),

            'active_status' => 1,
        ];

        // Log::info('Preparing to call Add Employee Type API...', $postData);

        // Make the HTTP POST request
        $response3 = $this->httpClient->request('POST', '/api/Employeetype/add', [
            'headers' => [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                'Content-Type' => 'application/json',
            ],
            'json' => $postData,
        ]);

        $modulesData = [];
        $modulesData = array_map(function ($module) {
            $formattedModule = ['module_id' => $module['module_id']];
            if (isset($module['sub_module_ids']) && is_array($module['sub_module_ids'])) {
                $formattedModule['sub_module_id'] = $module['sub_module_ids'];
            }
            return $formattedModule;
        }, $request->input('modulesData'));
        // Log::info('Sending module data to external API:', $modulesData);
        // $this->httpClient->request('POST', 'V1/corporate/corporate-components/addComponents', [
        //     'json' => [
        //         'corporate_id' => $request->input('accountDetails.corporate_id'),
        //         'components' => $modulesData,
        //     ],
        //     'headers' => $this->buildHeaders($request),
        // ]);
        $response4 = $this->httpClient->request('POST', 'V1/corporate/corporate-components/addComponents', [
            'json' => [
            'corporate_id' => $request->input('address.corporate_id'),
            'components' => $modulesData,

            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ],
        ]);

        $corporateAdminUserData = $this->buildCorporateAdminUserData($request);
        // Log::info('Sending corporate admin user data to external API:', $corporateAdminUserData);
        $response5 = $this->httpClient->request('POST', 'api/Corporate_admin_user/create', [
            'multipart' => $corporateAdminUserData,
            'headers' => [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ],
        ]);

        

              
        // Log::info('$response1',$response1);
        // Log::info('$response2',$response2);
        // Log::info('$response3',$response3);
        // Log::info('$response4',$response4);
        // Log::info('$response5',$response5);




        // $responseBody = json_decode($response->getBody()->getContents(), true);

        // // Log::info('Received response from external API:', [
        //     'status_code' => $response->getStatusCode(),
        //     'response_body' => $responseBody,
        // ]);

        if ($response5['success']) {
            // Return or handle the success message
            return response()->json([
                'success' => true,
                // 'message' => $response5['message'], // The success message from the API response
                  // The data returned by the API
            ]);
        }
        else{
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
        'location_id' => $request->input('address.corporate_id'),
        'corporate_id' => $request->input('address.corporate_id'),
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
        ['name' => 'corporate_admin_user_id', 'contents' => $request->input('corporateAdminUser.corporate_admin_user_id')],
        ['name' => 'location_id', 'contents' => $request->input('accountDetails.corporate_id')],

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


    public function corporateIndex(Request $request)
    {
        try {

            $response = $this->httpClient->request('POST', '/api/corporate/corporate_index', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            
             $corporate=($response['data']);
        
          
            if ($corporate['status'] == 200) {
                
                return view('content.form-wizard.corporate_list', [
                    'corporate' => $corporate['data'],
                ]);
            } else {
             
                Log::warning('Unexpected API response in corporateIndex.', ['response' => $data]);
                return redirect()->back()->withErrors('Unable to fetch corporate data.');
            }
        } catch (RequestException $e) {
       
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : null;

            Log::error('HTTP RequestException in corporateIndex.', [
                'message' => $e->getMessage(),
                'status_code' => $statusCode,
            ]);

            return redirect()->back()->withErrors('An error occurred while connecting to the API.');
        } catch (\Exception $e) {
            // Handle generic exceptions
            Log::error('Exception in corporateIndex.', ['message' => $e->getMessage()]);
            return redirect()->back()->withErrors('An unexpected error occurred.');
        }
    }




 













}