<?php

namespace App\Http\Controllers\Corporate\CorporateComponents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Corporate\CorporateComponents\CorporateComponentModules;
use App\Models\Corporate\CorporateComponents\CorporateComponentSubmodules;
use App\Models\Corporate\CorporateComponents\CorporateComponents;
use App\Models\MasterCorporate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CorporateMenuRights;
use App\Models\OhcMenuRights;
use Illuminate\Support\Facades\Auth;
use App\Models\CorporateAdminUser;

class ModulesController extends Controller
{
    public function getAllModules()
    {
        $modules = CorporateComponentModules::all();
        return response()->json([
            'status' => 'success',
            'data' => $modules
        ], 200);
    }
    public function getsubModules()
    {
        $modules = CorporateComponentSubmodules::all();
        return response()->json([
            'status' => 'success',
            'data' => $modules
        ], 200);
    }
    public function addModule(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'nullable|string',
            'module_name' => 'required|string|max:255',
        ]);
        $moduleName = $validated['module_name'];
        try {
            $existingModule = CorporateComponentModules::where('module_name', $moduleName)->first();
            if ($existingModule) {
                return response()->json([
                    'error' => 'A module with the same name already exists.',
                ], 400);
            }
            $moduleId = null;
            if (empty($validated['module_id']) || $validated['module_id'] === 'auto') {
                $lastModuleId = CorporateComponentModules::max('module_id');
                $moduleId = $lastModuleId ? $lastModuleId + 1 : 1;
            } else {
                $moduleId = (int) $validated['module_id'];
                $exists = CorporateComponentModules::where('module_id', $moduleId)->exists();
                if ($exists) {
                    return response()->json([
                        'error' => 'The provided module_id already exists.',
                    ], 400);
                }
            }
            $module = CorporateComponentModules::create([
                'module_id' => $moduleId,
                'module_name' => $moduleName,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Module added successfully.',
                'data' => $module
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while adding the module.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function showmodule(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'module_id' => 'required|integer|exists:corporate_component_modules,module_id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $module = CorporateComponentModules::where('module_id', $request->module_id)->firstOrFail();
            return response()->json([
                $module
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Module not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show_submodule(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'sub_module_id' => 'required|integer|exists:corporate_component_submodules,sub_module_id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $submodule = CorporateComponentSubmodules::where('sub_module_id', $request->sub_module_id)->firstOrFail();
            return response()->json([
                'success' => true,
                'data' => $submodule,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Submodule not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function editSubModule(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|integer|exists:corporate_component_modules,module_id',
            'sub_module_id' => 'nullable|integer|exists:corporate_component_submodules,sub_module_id',
            'sub_module_name' => 'nullable|string|max:255',
            'new_sub_module_name' => 'required|string|max:255',
        ]);
        try {
            $subModule = CorporateComponentSubmodules::when($request->has('sub_module_id'), function ($query) use ($request) {
                return $query->where('sub_module_id', $request->sub_module_id);
            })->when($request->has('sub_module_name'), function ($query) use ($request) {
                return $query->where('sub_module_name', $request->sub_module_name);
            })->first();
            if (!$subModule) {
                return response()->json(['error' => 'Submodule not found.'], 404);
            }
            $subModule->update([
                'sub_module_name' => $validated['new_sub_module_name'],
                'module_id' => $validated['module_id']
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Submodule name updated successfully.',
                'data' => $subModule
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while updating the submodule.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function editModule(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'nullable|integer|exists:corporate_component_modules,module_id',
            'module_name' => 'nullable|string|max:255',
            'new_module_name' => 'required|string|max:255|unique:corporate_component_modules,module_name',
        ]);
        try {
            $module = CorporateComponentModules::when($request->has('module_id'), function ($query) use ($request) {
                return $query->where('module_id', $request->module_id);
            })->when($request->has('module_name'), function ($query) use ($request) {
                return $query->where('module_name', $request->module_name);
            })->first();
            if (!$module) {
                return response()->json(['error' => 'Module not found.'], 404);
            }
            $module->update(['module_name' => $validated['new_module_name']]);
            return response()->json([
                'success' => true,
                'message' => 'Module name updated successfully.',
                'data' => $module
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while updating the module.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function deleteSubModule(Request $request)
    {
        $validated = $request->validate([
            'sub_module_id' => 'nullable|integer|exists:corporate_component_submodules,sub_module_id',
            'sub_module_name' => 'nullable|string|max:255',
        ]);
        try {
            $subModules = CorporateComponentSubmodules::when($request->has('sub_module_id'), function ($query) use ($request) {
                return $query->where('sub_module_id', $request->sub_module_id);
            })->when($request->has('sub_module_name'), function ($query) use ($request) {
                return $query->where('sub_module_name', $request->sub_module_name);
            });
            if (!$subModules->exists()) {
                return response()->json(['error' => 'Submodule not found.'], 404);
            }
            $deletedCount = $subModules->delete();
            return response()->json([
                'success' => true,
                'message' => "Deleted $deletedCount submodules successfully.",
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while deleting the submodule.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function deleteModule(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'nullable|integer|exists:corporate_component_modules,module_id',
            'module_name' => 'nullable|string|max:255',
        ]);
        try {
            $module = CorporateComponentModules::when($request->has('module_id'), function ($query) use ($request) {
                return $query->where('module_id', $request->module_id);
            })->when($request->has('module_name'), function ($query) use ($request) {
                return $query->where('module_name', $request->module_name);
            })->first();
            if (!$module) {
                return response()->json(['error' => 'Module not found.'], 404);
            }
            $moduleId = $module->module_id;
            $module->delete();
            $deletedSubmodulesCount = CorporateComponentSubmodules::where('module_id', $moduleId)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Module and associated submodules deleted successfully.',
                'details' => [
                    'deleted_module_id' => $moduleId,
                    'deleted_submodules_count' => $deletedSubmodulesCount,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while deleting the module.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function enableModuleLink(Request $request)
    {
        $validated = $request->validate([
            'corporate_id' => 'required|string|exists:master_corporate,corporate_id',
            'module_id' => 'required|integer|exists:corporate_component_modules,module_id',
        ]);
        try {
            $existingComponent = CorporateComponents::where('corporate_id', $request->corporate_id)
                ->where('module_id', $request->module_id)
                ->first();
            if ($existingComponent) {
                return response()->json([
                    'error' => 'Module is already linked.',
                ], 400);
            }
            $corporateComponent = new CorporateComponents();
            $corporateComponent->corporate_id = $request->corporate_id;
            $corporateComponent->module_id = $request->module_id;
            $corporateComponent->sub_module_id = [];
            $corporateComponent->save();
            return response()->json([
                'success' => true,
                'message' => 'Module linked successfully.',
                'data' => $corporateComponent,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while linking the module.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function enableSubModuleLink(Request $request)
    {
        $validated = $request->validate([
            'corporate_id' => 'required|string|exists:corporate_components,corporate_id',
            'module_id' => 'required|integer|exists:corporate_components,module_id',
            'sub_module_id' => 'present|array',
            'sub_module_id.*' => 'integer|exists:corporate_component_submodules,sub_module_id',
        ]);
        $corporateId = $validated['corporate_id'];
        $moduleId = $validated['module_id'];
        $subModuleIds = $validated['sub_module_id'];
        try {
            $corporateComponent = CorporateComponents::where('corporate_id', $corporateId)
                ->where('module_id', $moduleId)
                ->first();
            if (!$corporateComponent) {
                return response()->json([
                    'error' => 'The specified corporate_id and module_id are not linked. Please link them first.',
                ], 400);
            }
            if (empty($subModuleIds)) {
                $corporateComponent->update([
                    'sub_module_id' => null
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'All submodules have been removed.',
                ], 200);
            }
            sort($subModuleIds);
            $pgArray = '{' . implode(',', $subModuleIds) . '}';
            $corporateComponent->update([
                'sub_module_id' => $pgArray,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Submodules have been successfully updated.',
                'data' => [
                    'corporate_id' => $corporateId,
                    'module_id' => $moduleId,
                    'sub_module_id' => $subModuleIds,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while enabling the submodule link.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function disableModuleLink(Request $request)
    {
        $validated = $request->validate([
            'corporate_id' => 'required|string|exists:corporate_components,corporate_id',
            'module_id' => 'required|integer|exists:corporate_components,module_id',
        ]);
        $corporateId = $validated['corporate_id'];
        $moduleId = $validated['module_id'];
        try {
            $deletedRows = CorporateComponents::where('corporate_id', $corporateId)
                ->where('module_id', $moduleId)
                ->delete();
            if ($deletedRows > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "All rows with corporate_id: $corporateId and module_id: $moduleId have been deleted.",
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "No rows found for corporate_id: $corporateId and module_id: $moduleId.",
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while disabling the module link.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function getAllComponents(Request $request)
    {
        try {
            $modules = CorporateComponentModules::with('subModules')->get();
            $responseData = $modules->map(function ($module) {
                return [
                    'module_id' => $module->module_id,
                    'module_name' => $module->module_name,
                    'sub_modules' => $module->subModules->map(function ($subModule) {
                        return [
                            'sub_module_id' => $subModule->sub_module_id,
                            'sub_module_name' => $subModule->sub_module_name,
                        ];
                    }),
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $responseData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while retrieving components.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function addComponents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'required|string',
            'components' => 'required|array',
            'components.*.module_id' => 'required|integer',
            'components.*.sub_module_id' => 'nullable|array',
            'components.*.sub_module_id.*' => 'integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $corporate_id = $request->input('corporate_id');
            foreach ($request->input('components') as $component) {
                $module_id = $component['module_id'];
                $sub_module_ids = $component['sub_module_id'] ?? [];
                $sub_module_ids_str = empty($sub_module_ids) ? null : '{' . implode(',', $sub_module_ids) . '}';
                CorporateComponents::create([
                    'corporate_id' => $corporate_id,
                    'module_id' => $module_id,
                    'sub_module_id' => $sub_module_ids_str,
                ]);
            }
            return response()->json([
                'message' => 'Components added successfully!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving the data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateComponents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'required|string',
            'components' => 'required|array',
            'components.*.module_id' => 'required|integer',
            'components.*.sub_module_id' => 'nullable|array',
            'components.*.sub_module_id.*' => 'integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        try {
            $corporate_id = $request->input('corporate_id');
            $requested_module_ids = array_column($request->input('components'), 'module_id');
            $existingComponents = CorporateComponents::where('corporate_id', $corporate_id)->get();
            $existing_module_ids = $existingComponents->pluck('module_id')->toArray();
            $modules_to_delete = array_diff($existing_module_ids, $requested_module_ids);
            if (!empty($modules_to_delete)) {
                CorporateComponents::where('corporate_id', $corporate_id)
                    ->whereIn('module_id', $modules_to_delete)
                    ->delete();
            }
            foreach ($request->input('components') as $component) {
                $module_id = $component['module_id'];
                $sub_module_ids = $component['sub_module_id'] ?? [];
                $sub_module_ids_str = empty($sub_module_ids) ? null : '{' . implode(',', $sub_module_ids) . '}';
                $existingComponent = CorporateComponents::where('corporate_id', $corporate_id)
                    ->where('module_id', $module_id)
                    ->first();
                if ($existingComponent) {
                    $existingComponent->update([
                        'sub_module_id' => $sub_module_ids_str,
                    ]);
                } else {
                    CorporateComponents::create([
                        'corporate_id' => $corporate_id,
                        'module_id' => $module_id,
                        'sub_module_id' => $sub_module_ids_str,
                    ]);
                }
            }
            return response()->json([
                'message' => 'Components updated successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // backend Code
    private function getAllComponentsByCorpId($corporate_id)
    {
    //    return 'hi';
        try {
            $corporateComponents = CorporateComponents::where('corporate_id', $corporate_id)->get();
            if ($corporateComponents->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No components found for this corporate ID',
                    'data' => []
                ], 200);
            }
            $menuData = [];
            foreach ($corporateComponents as $component) {
                $module = CorporateComponentModules::where('module_id', $component->module_id)->first();
                if (!$module) {
                    continue;
                }
                $subModuleIds = [];
                if (!empty($component->sub_module_id)) {
                    if (is_string($component->sub_module_id)) {
                        $subModuleIds = array_map('intval', explode(',', trim($component->sub_module_id, '{}')));
                    } elseif (is_array($component->sub_module_id)) {
                        $subModuleIds = $component->sub_module_id;
                    }
                }
                $submodules = [];
                if (!empty($subModuleIds)) {
                    $submodules = CorporateComponentSubmodules::whereIn('sub_module_id', $subModuleIds)
                        ->where('module_id', $component->module_id)
                        ->get()
                        ->map(function ($submodule) {
                            return [
                                'sub_module_id' => $submodule->sub_module_id,
                                'sub_module_name' => $submodule->sub_module_name
                            ];
                        })
                        ->toArray();
                }
                $moduleData = [
                    'id' => $component->id,
                    'module_id' => $module->module_id,
                    'module_name' => $module->module_name,
                    'submodules' => $submodules
                ];
                $menuData[] = $moduleData;
            }
            return response()->json([
                'result' => true,
                'data' => $menuData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An unexpected error occurred while retrieving components.',
            ], 500);
        }
    }
    public function getAllComponentsByCorpIdByAccessRights(Request $request, $corporate_id)
    {
       // return 'Hi';
        $response = $this->getAllComponentsByCorpId($corporate_id);
        $responseData = json_decode($response->getContent(), true);
        $userId = auth('api')->user()->id;
        $mhcRights = CorporateMenuRights::where('corporate_user_id', $userId)->first();
        $ohcRights = OhcMenuRights::where('corporate_user_id', $userId)->first();
        $isSuperAdmin = CorporateAdminUser::where('id', $userId)->where('super_admin', 1)->exists();
        if (!$mhcRights && !$ohcRights) {
            return response()->json([
                'result' => false,
                'message' => 'No menu rights available for this user.'
            ], 403);
        }
        $mhcRightsMap = [
            'Diagnostic Assessment' => 'diagnostic_assessment',
            'Health Risk Assessment' => 'hra',
            'Events' => 'events',
            'Reports' => 'reports'
        ];
        $ohcRightsMap = [
            'Health Registry' => 'out_patient',
            'Prescription' => 'prescription',
            'Test' => 'tests',
            'Safety' => 'safety_board',
            'Reports' => 'ohc_report',
            'Census Report' => 'census_report',
            'Bio-Medical Waste' => 'bio_medical',
            'Inventory' => 'inventory',
            'Invoice' => 'invoice',
            'Forms' => 'forms',
            'Stocks' => 'stocks',
        ];
        $filteredData = [];
        foreach ($responseData['data'] as $module) {
            $moduleName = $module['module_name'];
            $filteredSubmodules = [];
            if ($moduleName === 'MHC' && $mhcRights) {
                foreach ($module['submodules'] as $submodule) {
                    $submoduleName = $submodule['sub_module_name'];
                    if (array_key_exists($submoduleName, $mhcRightsMap)) {
                        $rightKey = $mhcRightsMap[$submoduleName];
                        if (isset($mhcRights->$rightKey) && $mhcRights->$rightKey !== "0") {
                            $filteredSubmodules[] = $submodule;
                        }
                    }
                }
                if (!empty($filteredSubmodules)) {
                    $moduleData = $module;
                    $moduleData['submodules'] = $filteredSubmodules;
                    $filteredData[] = $moduleData;
                }
            } elseif ($moduleName === 'OHC' && $ohcRights) {
                foreach ($module['submodules'] as $submodule) {
                    $submoduleName = $submodule['sub_module_name'];
                    if ($submoduleName === 'OTC') {
                        $submoduleName = 'Prescription';
                    }
                    if (array_key_exists($submoduleName, $ohcRightsMap)) {
                        $rightKey = $ohcRightsMap[$submoduleName];
                        if (isset($ohcRights->$rightKey) && $ohcRights->$rightKey !== "0") {
                            $filteredSubmodules[] = $submodule;
                        }
                    }
                }
                if (!empty($filteredSubmodules)) {
                    $moduleData = $module;
                    $moduleData['submodules'] = $filteredSubmodules;
                    $filteredData[] = $moduleData;
                }
            } elseif ($moduleName === 'Others' && $ohcRights) {
                foreach ($module['submodules'] as $submodule) {
                    $submoduleName = $submodule['sub_module_name'];
                    if (array_key_exists($submoduleName, $ohcRightsMap)) {
                        $rightKey = $ohcRightsMap[$submoduleName];
                        if (isset($ohcRights->$rightKey) && $ohcRights->$rightKey !== "0") {
                            $filteredSubmodules[] = $submodule;
                        }
                    }
                }
                if (!empty($filteredSubmodules)) {
                    $moduleData = $module;
                    $moduleData['submodules'] = $filteredSubmodules;
                    $filteredData[] = $moduleData;
                }
            } else {
                $filteredData[] = $module;
            }
        }
        return response()->json([
            'result' => true,
            'data' => $filteredData,
            'is_super_admin' => $isSuperAdmin,
            'ohc_menu_rights' => $ohcRights,
            'mhc_menu_rights' => $mhcRights
        ], 200);
    }
}
