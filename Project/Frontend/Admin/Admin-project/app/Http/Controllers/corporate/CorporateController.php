<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CorporateController extends Controller
{
    protected $httpClient;

    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function editcorporate(Request $request, $id)
    {
        try {


            $response = $this->httpClient->request('GET', "/api/corporate/editcorporate/{$id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            if ($response['success'] == true) {
                $responseBody = $response['data'];
                $corporate = $responseBody['corporate'];
                // dd($corporate);
                return view('content.corporate_list.corporate.editcorporate', compact('corporate'));
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch corporate details.',
                ], 400);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client error occurred.',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function updatecorporate(Request $request, $id)
    {
        // dd($request->all());
        // Validation rules and messages
        $rules = [
            'corporate_name' => 'nullable|string',
            'corporate_id' => 'nullable|string',
            'display_name' => 'nullable|string',
            'corporate_no' => 'nullable|string',
            'registration_no' => 'nullable|string',
            'industry' => 'nullable|string',
            'prof_image' => 'nullable', // Updated validation for file
            'company_profile' => 'nullable|string',
            'gstin' => 'nullable',
            'discount' => 'nullable',
            'industry_segment' => 'nullable|string',
            'created_by' => 'nullable|string',
            'created_on' => 'nullable|date',
            'valid_from' => 'nullable|date',
            'valid_upto' => 'nullable|date',
            'corporate_color' => 'nullable|string',
            'active_status' => 'nullable|boolean',
        ];

        $messages = [
            'corporate_name.required' => 'The corporate name is required.',
            'corporate_id.string' => 'Corporate ID must be a string.',
            'prof_image.mimes' => 'The profile image must be a file of type: jpg, jpeg, png.',
            'prof_image.max' => 'The profile image may not be greater than 2MB.',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $validatedData = $validator->validated();

            // Prepare JSON data
            $data = [
                'corporate_id' => $validatedData['corporate_id'],
                'location_id' => $validatedData['corporate_id'],
                'corporate_name' => $validatedData['corporate_name'],
                'display_name' => $validatedData['display_name'],
                'corporate_no' => $validatedData['corporate_no'],
                'registration_no' => $validatedData['registration_no'],
                'industry' => $validatedData['industry'],
                'industry_segment' => $validatedData['industry_segment'],
                'company_profile' => $validatedData['company_profile'],
                'created_by' => $validatedData['created_by'],
                'valid_from' => $validatedData['valid_from'],
                'valid_upto' => $validatedData['valid_upto'],
                'gstin' => $validatedData['gstin'],
                'discount' => $validatedData['discount'],
                'corporate_color' => $validatedData['corporate_color'],
                'active_status' => $validatedData['active_status'],
            ];

            // Check for file upload
            $multipart = [];
            foreach ($data as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }

            if ($request->hasFile('prof_image')) {
                $file = $request->file('prof_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('/profile/prof_image', $filename, 'public');
                //dd($filePath);
                //exit();
                $multipart[] = [
                    'name' => 'prof_image',
                    'contents' => $filePath,

                ];
            }
            // Log the data being sent
            // Log::info('Sending multipart data to external API', ['multipart' => $multipart]);

            // Make API request
            $response = $this->httpClient->request('POST', "/api/corporate/update_corporate/{$id}", [
                'multipart' => $multipart,
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            // Log API response
            $responseBody = $response;


            if ($responseBody['success'] == true) {
                return redirect()->route('corporate-list')->with('success', 'Corporate updated successfully!');
            } else {
                Log::error('API responded with error', [
                    'message' => $responseBody['message'],
                ]);
                return redirect()
                    ->back()->with('error', 'Corporate update failed. Please try again.');
            }
        } catch (\Throwable $e) {
            // Log detailed error
            Log::error('Error updating corporate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()
                ->back()->with('error', 'An unexpected error occurred while updating the corporate.');
        }
    }
}
