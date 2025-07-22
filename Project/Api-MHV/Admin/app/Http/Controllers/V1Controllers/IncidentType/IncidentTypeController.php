<?php

namespace App\Http\Controllers\V1Controllers\IncidentType;

use App\Http\Controllers\Controller;
use App\Models\IncidentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\OhcComponents;

class IncidentTypeController extends Controller
{
    public function getAllIncidentTypes(Request $request)
    {
        $incidentTypes = IncidentType::select('incident_type_id', 'incident_type_name')->get();
        return response()->json([
            'result' => true,
            'data' => $incidentTypes
        ]);
    }
    public function addIncidentType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'incident_type_name' => 'required|string|unique:incident_types,incident_type_name'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'data' => $validator->errors()->first('incident_type_name')
            ], 422);
        }
        IncidentType::create([
            'incident_type_name' => $request->incident_type_name
        ]);
        return response()->json([
            'result' => true,
            'data' => 'Incident type added successfully.'
        ]);
    }
    public function editIncidentType(Request $request, $incident_type_id)
    {
        $incidentType = IncidentType::find($incident_type_id);
        if (!$incidentType) {
            return response()->json([
                'result' => false,
                'data' => 'Incident type not found.'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'incident_type_name' => 'required|string|unique:incident_types,incident_type_name,' . $incident_type_id . ',incident_type_id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'data' => $validator->errors()->first('incident_type_name')
            ], 422);
        }
        $incidentType->incident_type_name = $request->incident_type_name;
        $incidentType->save();
        return response()->json([
            'result' => true,
            'data' => 'Incident type updated successfully.'
        ]);
    }
    public function deleteIncidentType($incident_type_id)
    {
        $incidentType = IncidentType::find($incident_type_id);
        if (!$incidentType) {
            return response()->json([
                'result' => false,
                'data' => 'Incident type not found.'
            ], 404);
        }
        $incidentType->delete();
        return response()->json([
            'result' => true,
            'data' => 'Incident type deleted successfully.'
        ]);
    }
    public function getAllAssignedIncidentTypes(Request $request, $corporate_id)
    {
        if (!$corporate_id) {
            return response()->json([
                'result' => false,
                'data' => 'Corporate ID is required.'
            ], 400);
        }
        $incidentTypes = OhcComponents::where('corporate_id', $corporate_id)->get();
        if ($incidentTypes->isEmpty()) {
            return response()->json([
                'result' => false,
                'data' => 'No assigned incident types found.'
            ], 404);
        }
        $filteredData = $incidentTypes->map(function ($item) {
            return [
                'corporate_id' => $item->corporate_id,
                'incident_type_id' => $item->incident_type_id,
                'injury_color_types' => $item->injury_color_types,
            ];
        });
        return response()->json([
            'result' => true,
            'data' => $filteredData
        ]);
    }
    public function assignIncidentTypes(Request $request, $corporate_id)
    {
        if (!$corporate_id) {
            return response()->json([
                'result' => false,
                'data' => 'Corporate ID is required.'
            ], 400);
        }
        $validator = Validator::make($request->all(), [
            'incident_types' => 'required|array',
            'incident_types.*.id' => 'required|integer|exists:incident_types,incident_type_id',
            'incident_types.*.injury_color_types' => 'required|array',
            'incident_types.*.injury_color_types.*' => ['regex:/^#[0-9A-Fa-f]{6}$/']
        ]);
        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'data' => "Invalid Request"
            ], 422);
        }
        OhcComponents::where('corporate_id', $corporate_id)->delete();
        foreach ($request->incident_types as $incidentType) {
            OhcComponents::create([
                'corporate_id' => $corporate_id,
                'incident_type_id' => $incidentType['id'],
                'injury_color_types' => $incidentType['injury_color_types']
            ]);
        }
        return response()->json([
            'result' => true,
            'data' => 'Incident types synced successfully.'
        ]);
    }
}
