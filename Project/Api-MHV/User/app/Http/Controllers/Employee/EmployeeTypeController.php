<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\EmployeeType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class EmployeeTypeController extends Controller
{
    public function index()
    {
        try {
            $employeeTypes = EmployeeType::all();
            return response()->json($employeeTypes);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve employee types'], 500);
        }
    }

    public function store(Request $request)
    {
        // Log::info($request->all());
        try {
            $request->validate([
                'employee_type_name' => 'required|array', // Validate as an array
                'employee_type_name.*' => 'required|string|max:255', // Validate each array element
                'active_status' => 'required|boolean',
                'corporate_id' => 'required|string',
            ]);

            $createdEmployeeTypes = [];

            foreach ($request->employee_type_name as $name) {
                $createdEmployeeTypes[] = EmployeeType::create([
                    'employee_type_name' => $name,
                    'active_status' => $request->active_status,
                    'corporate_id' => $request->corporate_id,
                ]);
            }

            return response()->json($createdEmployeeTypes, 201); // Return all created rows
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create employee types', 'error' => $e->getMessage()], 500);
        }
    }
    public function add(Request $request)
    {
        try {
            $request->validate([
                'employee_type_name' => 'required',
                'active_status' => 'required|boolean',
                'corporate_id' => 'required|string',
            ]);
            $employeetype = EmployeeType::create([
                'employee_type_name' =>  ucwords($request->employee_type_name),
                'active_status' => $request->active_status,
                'corporate_id' => $request->corporate_id,
            ]);
            return response()->json($employeetype, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create employee types', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($corporate_id)
    {
       // return 'Haiu';
        try {
            $employeeTypes = EmployeeType::where('corporate_id', $corporate_id)->get();
            //return  $employeeTypes;
            if ($employeeTypes->isEmpty()) {
                return response()->json(['message' => 'No employee types found for the given corporate ID'], 404);
            }

            return response()->json(['data' => $employeeTypes], 200);
        } catch (Exception $e) {
            Log::error('Error retrieving employee types', [
                'corporate_id' => $corporate_id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['message' => 'An error occurred while retrieving employee types'], 500);
        }
    }



    public function update(Request $request)
    {
        // Log::info('Starting employee type update', ['data' => $request->all()]);

        try {
            $validator = Validator::make($request->all(), [
                '*.employee_type_id' => 'nullable|integer', // Allow null for new entries
                '*.employee_type_name' => 'required|string|max:255',
                '*.active_status' => 'required|boolean',
                '*.corporate_id' => 'required|string|max:255', // Ensure corporate_id is provided
                '*.checked' => 'nullable',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed for employee type update', [
                    'errors' => $validator->errors()->toArray(),
                    'data' => $request->all(),
                ]);
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $updatedEmployeeTypes = [];

            foreach ($request->all() as $employeeTypeData) {
                // Log::info('Processing employee type data', $employeeTypeData);

                if (!empty($employeeTypeData['employee_type_id'])) {
                    // Update existing employee type
                    $employeeType = EmployeeType::find($employeeTypeData['employee_type_id']);
                    if ($employeeType) {
                        $employeeType->update([
                            'employee_type_name' => $employeeTypeData['employee_type_name'],
                            'active_status' => $employeeTypeData['active_status'],
                            'corporate_id' => $employeeTypeData['corporate_id'],
                            'checked' => $employeeTypeData['checked'],
                        ]);
                        $updatedEmployeeTypes[] = $employeeType;
                        // Log::info('Employee type updated', $employeeTypeData);
                    } else {
                        Log::warning('Employee type not found', [
                            'employee_type_id' => $employeeTypeData['employee_type_id']
                        ]);
                    }
                } else {
                    // Create new employee type
                    $newEmployeeType = EmployeeType::create([
                        'employee_type_name' => $employeeTypeData['employee_type_name'],
                        'active_status' => $employeeTypeData['active_status'],
                        'corporate_id' => $employeeTypeData['corporate_id'],
                        'checked' => $employeeTypeData['checked'],

                    ]);
                    $updatedEmployeeTypes[] = $newEmployeeType;
                    // Log::info('New employee type created', $employeeTypeData);
                }
            }

            // Log::info('Employee types processed successfully', ['updated_employee_types' => $updatedEmployeeTypes]);

            return response()->json($updatedEmployeeTypes, 200);
        } catch (Exception $e) {
            Log::error('Failed to process employee types', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to process employee types',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $employeeType = EmployeeType::findOrFail($id);
            $employeeType->delete();

            return response()->json(['message' => 'Employee type deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Employee type not found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete employee type'], 500);
        }
    }
}
