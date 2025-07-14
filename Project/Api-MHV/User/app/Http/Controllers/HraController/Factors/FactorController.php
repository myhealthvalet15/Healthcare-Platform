<?php

namespace App\Http\Controllers\HraController\Factors;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hra\Factors\HraFactor;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use App\Models\Hra\Questions\HraQuestions;
use App\Models\Hra\Templates\HraTemplate;
use App\Models\Hra\Templates\HraTemplateQuestions;

class FactorController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'factor_name' => 'required|string|unique:hra_factors,factor_name',
            'active_status' => 'nullable|boolean',
            'priority' => 'nullable|integer',
        ]);
        $factor = new HraFactor();
        $factor->factor_name = $validatedData['factor_name'];
        $factor->active_status = $validatedData['active_status'] ?? 0;
        $factor->priority = $validatedData['priority'] ?? null;
        $factor->save();
        return response()->json(['message' => 'Factor created successfully', 'data' => $factor], 201);
    }
    public function getSuggestedFactorId()
    {
        $maxFactorId = HraFactor::max('factor_id');
        $suggestedId = $maxFactorId ? $maxFactorId + 1 : 1;
        return response()->json(['suggested_factor_id' => $suggestedId], 200);
    }
    public function getAllFactors()
    {
        $factors = HraFactor::all();
        if ($factors->isEmpty()) {
            return response()->json(['data' => []], 200);
        }
        return response()->json(['data' => $factors], 200);
    }
    public function getFactorById($factorId)
    {
        $factor = HraFactor::where('factor_id', $factorId)->first();
        if (!$factor) {
            return response()->json(['message' => 'Factor not found'], 404);
        }
        return response()->json(['data' => $factor], 200);
    }
    public function editFactor(Request $request, $factorId)
    {
        $factor = HraFactor::where('factor_id', $factorId)->first();
        if (!$factor) {
            return response()->json(['message' => 'Factor not found'], 404);
        }
        $validatedData = $request->validate([
            'new_factor_name' => [
                'required',
                'string',
                Rule::unique('hra_factors', 'factor_name')->ignore($factorId, 'factor_id'),
            ],
            'active_status' => 'required|boolean',
        ]);
        $factor->factor_name = $validatedData['new_factor_name'];
        $factor->active_status = $validatedData['active_status'];
        $factor->save();
        return response()->json(['message' => 'Factor updated successfully', 'data' => $factor], 200);
    }
    public function deleteFactor($factorId)
    {
        try {
            $factor = HraFactor::where('factor_id', $factorId)->first();
            if (!$factor) {
                return response()->json(['result' => 'failed', 'message' => 'Factor not found'], 404);
            }
            HraTemplateQuestions::where('factor_id', $factorId)->delete();
            HraTemplate::where('factor_id', $factorId)->delete();
            $factor->delete();
            return response()->json(['result' => 'success', 'message' => 'Factor and all related records deleted successfully'], 200);
        } catch (QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json([
                    'result' => 'failed',
                    'message' => 'This factor is being used in other relations. Please remove associations and try again.',
                    'error' => $e->getMessage()
                ], 400);
            }
            return response()->json([
                'result' => 'failed',
                'message' => 'An error occurred while deleting the factor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getPriority($factorId)
    {
        $factor = HraFactor::where('factor_id', $factorId)->first();
        if (!$factor) {
            return response()->json(['message' => 'Factor not found'], 404);
        }
        return response()->json(['priority' => $factor->priority], 200);
    }
    public function setPriority(Request $request, $factorId)
    {
        $validatedData = $request->validate([
            'priority' => 'required|integer',
        ]);
        $factor = HraFactor::where('factor_id', $factorId)->first();
        if (!$factor) {
            return response()->json(['message' => 'Factor not found'], 404);
        }
        if ($validatedData['priority'] === 0) {
            $factor->priority = null;
        } else {
            $existingFactor = HraFactor::where('priority', $validatedData['priority'])
                ->where('factor_id', '!=', $factorId)
                ->first();
            if ($existingFactor) {
                return response()->json([
                    'message' => "Priority {$validatedData['priority']} is already set to {$existingFactor->factor_name}."
                ], 400);
            }
            $factor->priority = $validatedData['priority'];
        }
        $factor->save();
        return response()->json(['message' => 'Factor priority updated successfully', 'data' => $factor], 200);
    }
    public function getActiveStatus($factorId)
    {
        $factor = HraFactor::where('factor_id', $factorId)->first();
        if (!$factor) {
            return response()->json(['message' => 'Factor not found'], 404);
        }
        return response()->json(['active_status' => $factor->active_status], 200);
    }
    public function setActiveStatus(Request $request, $factorId)
    {
        $validatedData = $request->validate([
            'active_status' => 'required|boolean',
        ]);
        $factor = HraFactor::where('factor_id', $factorId)->first();
        if (!$factor) {
            return response()->json(['message' => 'Factor not found'], 404);
        }
        $factor->active_status = $validatedData['active_status'];
        $factor->save();
        return response()->json(['message' => 'Factor active status updated successfully', 'data' => $factor], 200);
    }
}
