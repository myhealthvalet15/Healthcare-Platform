<?php

namespace App\Http\Controllers\doctor_qualification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Services\GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class DoctorController extends Controller
{
    protected $httpClient;

    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function index(Request $request)
    {
        try {
            $response = $this->httpClient->request('POST', 'api/doctor/index', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            $data = $response['data'];

            $doctors = (isset($data) ? $data : []);
            return view('content.doctor_qualifications.doctor_qualification', compact('doctors'));
        } catch (\Exception $e) {
        }
    }
    public function store(Request $request)
    {


        // Log::info('request_sts', $request->all());

        $request->validate([
            'qualification_name' => 'required|string|max:255',
            'qualification_type' => 'required',
            'active_status' => 'required',
        ]);

        try {
            $response = $this->httpClient->request('POST', '/api/doctor/add', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'qualification_name' => $request->input('qualification_name'),
                    'qualification_type' => $request->input('qualification_type'),
                    'active_status' => $request->input('active_status'),
                ],
            ]);


            if ($response['success']) {

                return back()->route('doctor.index')->with('success', 'Doctor qualification added successfully!');
            } else {
                return back()->with('error', 'Failed to add Doctor qualification. Status code: ');
            }
        } catch (\Exception $e) {
            Log::error('Add doctot qualification  API Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to add Doctor qualification. Please try again later.');
        }
    }
    public function update(Request $request)
    {

        try {

            // Log::info('Request data:', $request->all());
            $request->validate([
                'qualification_name' => 'required|string|max:255',
                'qualification_id' => 'required',
                'active_status' => 'required',
            ]);
            $qualification_id = $request->qualification_id;

            $response = $this->httpClient->request('POST', "/api/doctor/update/{$qualification_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'qualification_name' => $request->input('qualification_name'),
                    'active_status' => $request->input('active_status'),

                ],
            ]);

            //$data = json_decode($response->getBody()->getContents(), true);
            if ($response['success']) {
                return response()->json(['message' => 'doctor qualification updated successfully!']);
            } else {
                return back()->with('error', 'Failed to add Doctor qualification. Status code: ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {

            Log::error('Error creating doctor qualification: ' . $e->getMessage());


            return response()->json(['error' => 'Failed to update doctor qualification.'], 500);
        }
    }
}
