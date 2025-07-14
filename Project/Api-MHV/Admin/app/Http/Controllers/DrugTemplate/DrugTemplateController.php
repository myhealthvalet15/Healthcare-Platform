<?php

namespace App\Http\Controllers\DrugTemplate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\DrugTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class DrugTemplateController extends Controller
{
    public function getAllDrugTemplates($request)
    {
        try {
            $drugtemplates = DrugTemplate::all();
            // Log::info('Starting Drug Template update', ['data' => $request->all()]);
            return response()->json($drugtemplates);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve drug template'], 500);
        }
    }
}
