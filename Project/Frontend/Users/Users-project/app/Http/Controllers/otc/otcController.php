<?php

namespace App\Http\Controllers\otc;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class OtcController extends Controller
{
   public function searchOTC()
    {
        $headerData = 'OTC Details';
        return view('content.otc.otc-add', ['HeaderData' => $headerData]);
   
    }
  
    public function addOTC($employee_id = null, $op_registry_id = null)
    {
        $locationId = session('location_id');

        if ($op_registry_id !== null && !is_numeric($op_registry_id)) {
            return "Invalid Request";
        }


        if (!$employee_id || !ctype_alnum($employee_id)) {
            return "Invalid Request";
        }
        $url = 'https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/checkEmployeeId/followUp/' . 0 .  '/' . $employee_id;
        if ($op_registry_id !== null) {
            $url .= "/op/" . $op_registry_id;
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . request()->cookie('access_token'),
        ])->get($url); 
        $pharmacyResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . request()->cookie('access_token'),
            ])->get('https://api-user.hygeiaes.com/V1/corporate/corporate-components/getPharmacyDetails/' . $locationId);
           

      //  return $response;
        if ($response->successful() && $pharmacyResponse->successful()) {
            $data = $response->json();
            if (!isset($data['result']) || !$data['result']) {
                return "Invalid Request";
            }
            $pharmacyData     = $pharmacyResponse->json();

            $headerData = 'Add OTC Details';
            if ($op_registry_id !== null) {
                return view('content.otc.edit-otc', [
                    'HeaderData' => $headerData,
                    'employeeData' => $data['message']
                ]);
            }
            return view('content.otc.add-otc', [
                'HeaderData' => $headerData,
                'pharmacyData' => $pharmacyData,
                'employeeData' => $data['message']
            ]);
        }
        return "Invalid Request";
    }
     public function storeOTC(Request $request)
      {
        $locationId = session('location_id');      
        $corporateId = session('corporate_id');
        $userId = session('corporate_admin_user_id');
        $requestData = array_merge($request->all(), [
            'location_id' => $locationId,
            'corporate_id' => $corporateId,
            'corporate_user_id' => $userId,
        ]);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-user.hygeiaes.com/V1/corporate/corporate-components/addPrescriptionForOTC', $requestData);
           // return $response;
            //return response()->json($response->json());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ');
        }
    }
    public function listOTC()
    {
        $headerData = 'List OTC Details';
        return view('content.otc.list-otc', ['HeaderData' => $headerData]);
   
    }
 public function listotcdetails(Request $request)
{
    $locationId = session('location_id');
  // return $employee_id = session('employee_id');        

    if (!$locationId) {
        return response()->json([
            'result' => false,
            'message' => 'Invalid Requestsssss'
        ]);
    }

    try {
        $url = 'https://api-user.hygeiaes.com/V1/corporate/corporate-components/getAllotcDetails/' . $locationId;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get($url);
        return $response;
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
 
        return response()->json(['result' => false, 'data' => 'Invalid request'], $response->status());
    } catch (\Exception $e) {
        return response()->json(['result' => false, 'data' => 'Error in Fetching data'], 503);
    }
}


}
