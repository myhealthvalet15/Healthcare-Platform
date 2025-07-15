<?php

namespace App\Http\Controllers\components\events;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EventsController extends Controller
{
    public function listEvents()
    {
        $headerData = "Events List";
        return view('content.components.mhc.events.list-events', ['HeaderData' => $headerData]);
    }
     public function addNewEvents()
    {
        $headerData = "Add New Events";
        return view('content.components.mhc.events.add-events', ['HeaderData' => $headerData]);
    }
  
    public function storeEvents(Request $request)
    {
       
        try {
            $corporateId = session('corporate_id');
            $locationId = session('location_id');
            if (! $corporateId || ! $locationId) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
            }
            $response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
])->post('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/storeEvents/' . $corporateId . '/' . $locationId, $request->all());
 //return $response;
            if ($response->successful()) {
                return response()->json(['result' => true, 'message' => $response['message']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }
    }
   public function getAllEventsByCorporate(Request $request)
    {
       // return 'hello';
        try {
            $corporateId = session('corporate_id');
            if (! $corporateId) {
                return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
            }
            $response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getAllEventsByCorporate/' . $corporateId);
//return $response;
            // return $response;          
if ($response->successful()) {     
                return response()->json(['result' => true, 'data' => $response['data']]);
            }
            return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
        }            

}
public function destroy($id,Request $request)
{
   // return $id;
        try {
       
        if (! $id) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->delete('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/destroy/' . $id);
        return $response;
        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => $response['message']]);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    } catch (\Exception $e) {
        return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
    }
}
public function editEvents($id)
{
    $headerData = "Edit Events";
   // $eventId = $id;
    return view('content.components.mhc.events.edit-events', ['HeaderData' => $headerData, 'eventId' => $id]);
}
public function getEventsById($id, Request $request)
{
    try {
        $corporateId = session('corporate_id');
        if (! $corporateId) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/getEventsById/' . $id . '/' . $corporateId);
        
        if ($response->successful()) {
            return response()->json(['result' => true, 'data' => $response['data']]);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    } catch (\Exception $e) {
        return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
    }

}
public function updateEvents($id,Request $request)
{
    //return $id;
    try {
        $corporateId = session('corporate_id');
        $locationId = session('location_id');
        if (! $corporateId || ! $locationId) {
            return response()->json(['result' => false, 'message' => 'Invalid Request'], 404);
        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->put('https://api-user.hygeiaes.com/V1/corporate-stubs/stubs/updateEvents/' . $id, $request->all());
      //  return $response;
        if ($response->successful()) {
            return response()->json(['result' => true, 'message' => $response['message']]);
        }
        return response()->json(['result' => false, 'message' => 'Invalid Request'], $response->status());
    } catch (\Exception $e) {
        return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
    }

}
}