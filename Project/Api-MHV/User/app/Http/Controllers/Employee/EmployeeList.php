<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Employee\EmployeeType;
use App\Http\Resources\EmployeeUserMappingResource;
use App\Models\Department\CorporateHl1;

class EmployeeList extends Controller
{
    public function show($corporate_id)
    {
        try {
            $employeeData = EmployeeUserMapping::with(['masterUser', 'corporateHL1', 'employeeType'])
                ->where('corporate_id', $corporate_id)
                ->get(['id', 'corporate_id', 'hl1_id', 'user_id', 'designation', 'employee_id', 'employee_type_id', 'corporate_contractors_id', 'from_date']);
            if ($employeeData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the given corporate_id',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee data retrieved successfully',
                'data' => EmployeeUserMappingResource::collection($employeeData)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving employee data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function fetchEmployeeType($corporate_id)
    {
        try {
            $employeeTypes = EmployeeType::where('corporate_id', $corporate_id)
                ->where('active_status', 1)
                ->get(['employee_type_id', 'employee_type_name', 'checked']);
            if ($employeeTypes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee types found for the given corporate_id',
                    'data' => []
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Employee types retrieved successfully',
                'data' => $employeeTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving employee types',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function fetchDepartment($corporate_id)
    {
        try {
            $departmentTypes = CorporateHl1::where('corporate_id', $corporate_id)
                ->where('active_status', 1)
                ->get(['hl1_id', 'hl1_name']);
            if ($departmentTypes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No department found for the given corporate_id',
                    'data' => []
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Department retrieved successfully',
                'data' => $departmentTypes
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving department',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
