<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Exception;


class CertificationController extends Controller
{
    protected $httpClient;

    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    public function index(Request $request)
    {

        $response = $this->httpClient->request('POST', '/api/certificate/index', [

            'headers' => [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ],
        ]);
        // $certification = json_decode($response->getBody()->getContents(), true);
        $certification = $response['data'];
        // Log::info('Certificate data:', $certification);
        //  dd($certification);

        if ($response['data']) {
            return view('content.corporate_list.corporate.certificate.certificate', compact('certification'));
        } else {
        }
    }


    public function store(Request $request)
    {
        // Log::info('Certificate registration request:', $request->all());

        $validator = Validator::make($request->all(), [
            'certification_title' => 'required|string|max:255',
            'short_tag' => 'required|string|max:100',
            'content' => 'required|string',
            'condition' => 'required|array',
            'color_condition' => 'required|array',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        // Log::info('Validated data:', $validated);

        $data = [
            'certification_title' => $validated['certification_title'],
            'short_tag' => $validated['short_tag'],
            'content' => $validated['content'],
            'conditions' => $validated['condition'],
            'color_conditions' => $validated['color_condition'],
        ];

        // Log::info('Prepared data for API request:', $data);

        try {
            // Log::info('Making API request to /api/certificate/create');
            $response = $this->httpClient->request('POST', '/api/certificate/create', [
                'json' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            dd($response);
            // $responseData = json_decode($response->getBody()->getContents(), true);
            //   // Log::info('API response received:', $responseData);

            if ($response['success']) {
                // Log::info('Certification created successfully');
                return redirect()->route('add-certification')->with('success', 'Certification created successfully');
            } else {
                Log::error('Error creating certification:', [
                    'message' => $response['message'] ?? 'Unknown error',
                    'response' => $response
                ]);
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while creating the certification:', ['exception' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $id)
    {
        $certificate_id = $id;

        $endpoint = "/api/certificate/show/{$certificate_id}";

        try {
            $response = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);

            $certificate = $response['data'];
            // dd($certificate);
            return view('content.corporate_list.corporate.certificate.editcertificate', compact('certificate'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch certificate details',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        // Log::info('Certificate registration request:', $request->all());

        $validator = Validator::make($request->all(), [
            'certification_title' => 'required|string|max:255',
            'short_tag' => 'required|string|max:100',
            'content' => 'required|string',
            'condition' => 'required|array',
            'color_condition' => 'required|array',
            'active_status' => 'required',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed during certificate update:', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);

            return back()->withErrors($validator)->withInput();
        }


        $certificateData = [
            'certification_title' => $request->input('certification_title'),
            'short_tag' => $request->input('short_tag'),
            'content' => $request->input('content'),
            'condition' => $request->input('condition'),
            'color_condition' => $request->input('color_condition'),
            'active_status' => $request->input('active_status'),
        ];


        $endpoint = "/api/certificate/update/{$id}";

        try {

            $response = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => $certificateData,
            ]);
            //dd($response);
            //$responseData = json_decode($response->getBody()->getContents(), true);
            //if ($response->getStatusCode() === 201) {
            // Log::info('Certification uupdated successfully');
            return redirect()->route('add-certification')->with('success', 'Certification updated successfully');
            // } else {
            //    Log::error('Error creating certification:', [
            //        'message' => $responseData['message'] ?? 'Unknown error',
            //        'response' => $responseData
            //   ]);
            // }
        } catch (\Exception $e) {
            Log::error('API request failed:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'An error occurred while updating the certificate.']);
        }
    }


    public function show(Request $request, $id)
    {
        $certificate_id = $id;

        $endpoint = "/api/certificate/show/{$certificate_id}";

        try {
            $response = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            $certification = $response['data'];
            // $certification = json_decode($response->getBody()->getContents(), true);

            return view('content.corporate_list.corporate.certificate.showcertificate', compact('certification'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to fetch certificate details',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
