<?php

namespace App\Http\Controllers\V1Controllers\CorporateController\CorporateComponents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\V1Models\Corporate\CorporateComponents\CorporateComponentModules;
use App\Models\V1Models\Corporate\CorporateComponents\CorporateComponentSubmodules;
use App\Models\MasterCorporate;
use App\Models\V1Models\Corporate\CorporateComponents\CorporateComponents;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\V1Models\Forms\CorporateForms;

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
    public function getModule(Request $request)
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
                'result' => true,
                'message' => $module
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
    public function getSubModule(Request $request)
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
        try {
            $validated = $request->validate([
                'module_id' => 'required|integer',
                'sub_module_id' => 'required|integer|exists:corporate_component_submodules,sub_module_id',
                'new_sub_module_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('corporate_component_submodules', 'sub_module_name')->ignore($request->sub_module_id, 'sub_module_id')
                ]
            ]);
            $moduleExists = CorporateComponentModules::where('module_id', $validated['module_id'])->exists();
            if (!$moduleExists) {
                return response()->json([
                    'result' => false,
                    'message' => 'Module not found'
                ], 404);
            }
            $subModule = CorporateComponentSubmodules::where([
                'module_id' => $validated['module_id'],
                'sub_module_id' => $validated['sub_module_id']
            ])->first();
            if (!$subModule) {
                return response()->json([
                    'result' => false,
                    'message' => 'The provided module_id and sub_module_id combination does not exist'
                ], 404);
            }
            $subModule->update([
                'sub_module_name' => $validated['new_sub_module_name']
            ]);
            return response()->json([
                'result' => true,
                'message' => 'Submodule updated successfully',
                'data' => $subModule
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'error' => 'An unexpected error occurred while updating the submodule.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function editModule(Request $request)
    {
        $validated = $request->validate([
            'module_id' => 'required|integer|exists:corporate_component_modules,module_id',
            'new_module_name' => 'required|string|max:255|unique:corporate_component_modules,module_name',
        ]);
        try {
            $module = CorporateComponentModules::where('module_id', $validated['module_id'])->first();
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
        // Load all modules with their subModules relationship
        $modules = CorporateComponentModules::with('subModules')->get();

        // Load corporate forms once and prepare them as an array
        $corporateForms = CorporateForms::select(
            'corporate_form_id as sub_module_id',
            'form_name as sub_module_name'
        )->get();

        // Map over modules and build the response structure
        $responseData = $modules->map(function ($module) use ($corporateForms) {
            // Collect submodules from the module
            $originalSubModules = collect($module->subModules)->map(function ($subModule) {
                return [
                    'sub_module_id' => $subModule->sub_module_id,
                    'sub_module_name' => $subModule->sub_module_name,
                ];
            });

            // If the module ID is 4, merge with corporate forms
            if ($module->module_id == 4) {
                $dynamicForms = collect($corporateForms)->map(function ($form) {
                    return [
                        'sub_module_id' => $form->sub_module_id,
                        'sub_module_name' => $form->sub_module_name,
                    ];
                });

                // Merge and remove duplicates by sub_module_id
                $mergedSubModules = $originalSubModules
                    ->merge($dynamicForms)
                    ->unique('sub_module_id')
                    ->values();
            } else {
                $mergedSubModules = $originalSubModules;
            }

            return [
                'module_id' => $module->module_id,
                'module_name' => $module->module_name,
                'sub_modules' => $mergedSubModules,
            ];
        });

        // Return success response
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
        //try {
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
        //return $request->all();
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
    }
    public function getAllComponentsByCorpId($corpid)
{
    try {
        if (!isset($corpid)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Corporate Id',
                'data' => []
            ], 400);
        }

        // Get corporate name
        $corp = MasterCorporate::where([
            ['corporate_id', $corpid],
            ['location_id', $corpid]
        ])->select('corporate_name')->first();

        // Get assigned components
        $corporateComponents = CorporateComponents::where('corporate_id', $corpid)->get();

        if ($corporateComponents->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No components found for this corporate ID',
                'data' => []
            ], 200);
        }

        $responseData = [];

        foreach ($corporateComponents as $component) {
            $module = CorporateComponentModules::where('module_id', $component->module_id)->first();
            if (!$module) {
                continue;
            }

            // Parse sub_module_id from DB (stored as string or array)
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
                if ($component->module_id == 4) {
                    // ✅ For Forms (module 4), get data from corporate_forms table
                    $submodules = CorporateForms::whereIn('corporate_form_id', $subModuleIds)
                        ->select('corporate_form_id as sub_module_id', 'form_name as sub_module_name')
                        ->get()
                        ->toArray();
                } else {
                    // ✅ For other modules, get data from standard submodules table
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
            }

            // Prepare module structure
            $moduleData = [
                'id' => $component->id,
                'module_id' => $module->module_id,
                'module_name' => $module->module_name,
                'submodules' => $submodules
            ];

            $responseData[] = $moduleData;
        }

        return response()->json([
            'success' => true,
            'data' => $responseData,
            'corpName' => optional($corp)->corporate_name
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'An unexpected error occurred while retrieving components.',
            'details' => $e->getMessage()
        ], 500);
    }
}

    public function addSubModule(Request $request)
    {
        try {
            $validated = $request->validate([
                'module_id' => 'required|integer|exists:corporate_component_modules,module_id',
                'sub_module_name' => 'required|string|max:255',
            ]);
            if (CorporateComponentSubmodules::where('sub_module_name', $validated['sub_module_name'])->exists()) {
                return response()->json([
                    'result' => false,
                    'message' => 'Sub-module name already exists'
                ], 400);
            }
            $lastSubModule = CorporateComponentSubmodules::orderBy('sub_module_id', 'desc')->first();
            $newSubModuleId = $lastSubModule ? $lastSubModule->sub_module_id + 1 : 1;
            $newSubModule = CorporateComponentSubmodules::create([
                'sub_module_id' => $newSubModuleId,
                'module_id' => $validated['module_id'],
                'sub_module_name' => $validated['sub_module_name'],
            ]);
            return response()->json([
                'result' => true,
                'message' => 'Sub-module added successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred while adding sub modules.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
  public function getModule4Submodules()
{
    $components = CorporateComponents::where('module_id', 4)->get();

    $formsBySubModule = [];

    foreach ($components as $component) {
        // Clean and convert "{1,3}" to [1, 3]
        $subModuleIds = collect(explode(',', trim($component->sub_module_id, '{}')))
            ->map(fn($id) => (int) $id)
            ->filter();

        // Get matching forms
        $forms = CorporateForms::whereIn('corporate_form_id', $subModuleIds)->get();

        foreach ($forms as $form) {
            $formsBySubModule[] = [
                'sub_module_id' => $form->corporate_form_id,
                'sub_module_name' => $form->form_name,
                'corporate_form_id' => $form->corporate_form_id,
            ];
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Request was successful.',
        'data' => [
            'components' => $components,
            'formsBySubModule' => $formsBySubModule,
        ]
    ]);
}
public function assignFormForLocation(Request $request)
{
   // return $request;
    // Validate the input
    $request->validate([
        'corporate_id' => 'required|string|max:255',
        'location_id' => 'required|string|max:255',
        'form_ids' => 'required|array|min:1',
        'form_ids.*' => 'numeric',
    ]);

    // Check if a record already exists
    $existing = DB::table('corporate_assigned_forms')
        ->where('corporate_id', $request->corporate_id)
        ->where('location_id', $request->location_id)
        ->first();

    if ($existing) {
        // Update with JSON form_ids
        DB::table('corporate_assigned_forms')
            ->where('id', $existing->id)
            ->update([
                'form_id' => json_encode($request->form_ids),
                'updated_at' => now(),
            ]);
    } else {
        // Insert new with JSON form_ids
        DB::table('corporate_assigned_forms')->insert([
            'corporate_id' => $request->corporate_id,
            'location_id' => $request->location_id,
            'form_id' => json_encode($request->form_ids),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Forms assigned successfully.',
        'redirect_url' => 'https://mhv-admin.hygeiaes.com/corporate/corporate-list'

    ]);
}


public function getAssignedForms($corporate_id, $location_id)
{
    //return $request;
    // Fetch the assigned forms for the corporate_id and location_id
    $assignedForms = DB::table('corporate_assigned_forms')
                        ->where('corporate_id', $corporate_id)
                        ->where('location_id', $location_id)
                        ->first();
    
    // Fetch the form IDs from the assigned form
    $selectedFormIds = $assignedForms ? json_decode($assignedForms->form_id) : [];

   
     return response()->json([
        'success' => true,
        'message' => 'Request was successful.',
        'data' => [
            'selectedFormIds' => $selectedFormIds
            
        ]
    ]);
}
}

