<?php

namespace App\Http\Controllers\V1Controllers\IncidentType;

use App\Http\Controllers\Controller;
use App\Models\IncidentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

}
