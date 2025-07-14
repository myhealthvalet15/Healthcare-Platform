<?php

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class Employees extends Controller
{
    public function displayEmployeeListPage(Request $request)
    {
        $headerData = "EMPLOYEE'S LIST";
        return view('content.Employee.list', [
            'HeaderData' => $headerData,
        ]);
    }

    public function getAllEmployeeList(Request $request)
    {
       // return 'Hieeeee';
        $corporate_id = session('corporate_id');
        $location_id = session('location_id');
        if (!$corporate_id || !$location_id) {
            return redirect()->route('dashboard-analytics');
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->post("https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllEmployeeData/" . $corporate_id . '/' . $location_id);
       // return $response;
        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => $response['message']]);
        } else {
            return response()->json(['result' => true, 'message' => $response['message']]);
        }
    }

  


}
