<?php

namespace App\Http\Controllers\corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class ComponentController extends Controller
{
    protected $httpClient;
    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    public function modules(Request $request)
    {
        $response = $this->httpClient->request('GET', 'V1/corporate/corporate-components/getAllModules', [
            'headers' => [
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ],
        ]);
        $data = $response['data'];
        $modules = $data['data'];
        if ($response['success']) {
            return view('content.corporate_list.corporate.components.module', compact('modules'));
        }
    }
    public function addModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_name' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $response = $this->httpClient->request('POST', 'V1/corporate/corporate-components/module/add-module', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
                'json' => [
                    'module_name' => $request->input('module_name'),
                ],
            ]);
            return back()->with('success', 'Module added successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function editModule(Request $request, $id)
    {
        if (!is_numeric($id)) {
            return back()->with('error', 'Invalid module ID');
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/module/get', [
                'module_id' => $id
            ]);
            if ($response->successful()) {
                $module = $response->json()['message'] ?? null;
                return view('content.corporate_list.corporate.components.editmodule', compact('module'));
            } else {
                return "No Module Found ..";
            }
            return back()->with('error', 'Failed to fetch module data');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function editSubModule(Request $request, $id)
    {
        if (!is_numeric($id)) {
            return back()->with('error', 'Invalid module ID');
        }
        try {
            $responseModule = $this->httpClient->request('GET', 'V1/corporate/corporate-components/getAllModules', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            $response = $this->httpClient->request('POST', 'V1/corporate/corporate-components/submodule/get', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
                'json' => [
                    'sub_module_id' => $id,
                ],
            ]);
            if (! $response['success']) {
                return "No Submodule Found ..";
            }
            $module = $responseModule;
            $submodule = $response['data']['data'];
            $modules = $module['data']['data'];
            if ($response['success']) {
                return view('content.corporate_list.corporate.components.edit_submodule', compact('modules', 'submodule'));
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function updateModule(Request $request, $id)
    {
        if (!is_numeric($id)) {
            return back()->with('error', 'Invalid module ID');
        }
        try {
            $validator = Validator::make($request->all(), [
                'new_module_name' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/module/edit', [
                'module_id' => $id,
                'new_module_name' => $request->input('new_module_name'),
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    return redirect()->route('modules')->with('success', 'Module updated successfully');
                }
                return back()->with('error', $data['message'] ?? 'Failed to update module');
            }
            return back()->with('error', 'Failed to connect to the server');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function updateSubModule(Request $request, $id)
    {
        // TODO: Module Cant be updated in the dropdown, this to be fix that
        if (!is_numeric($id)) {
            return back()->with('error', 'Invalid module ID');
        }
        try {
            $validator = Validator::make($request->all(), [
                'new_sub_module_name' => 'required|string|max:255',
                'module_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->put('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/submodule/edit', [
                'module_id' => $request->input('module_id'),
                'sub_module_id' => $id,
                'new_sub_module_name' => $request->input('new_sub_module_name'),
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    return redirect()->route('corporate-component-sub-module')->with('success', 'Sub Module updated successfully');
                }
                return back()->with('error', $data['message'] ?? 'Failed to update sub module');
            }
            return back()->with('error', 'Failed to connect to the server');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function submodules(Request $request)
    {
        try {
            $responseModule = $this->httpClient->request('GET', 'V1/corporate/corporate-components/getAllModules', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            $submodules = $this->httpClient->request('GET', 'V1/corporate/corporate-components/getsubModules', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            $module = $submodules;
            $submodule = $responseModule;
            $submodules = $module['data']['data'];
            $modules = $submodule['data']['data'];
            return view('content.corporate_list.corporate.components.submodule', compact('modules', 'submodules'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    public function addSubModule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|numeric',
            'sub_module_name' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/submodule/add-sub-module', [
                'module_id' => $request->input('module_id'),
                'sub_module_name' => $request->input('sub_module_name')
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    return back()->with('success', 'Sub Module added successfully!');
                }
                return back()->with('error', $data['message'] ?? 'Failed to add sub module');
            }
            return back()->with('error', 'Failed to connect to the server');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while adding the sub module');
        }
    }
    public function editComponents(Request $request, $id, $corporate_id)
    {
        $request->session()->put('component_edit_id', $corporate_id);
        $componentEditId = Session::get('component_edit_id');

        try {
            // ✅ Get selected components for this corporate
            $response = $this->httpClient->request('GET', "V1/corporate/corporate-components/getAllComponent/corpId/{$corporate_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);

            $modulesData = $response['data'];
            $components = $modulesData['data'] ?? [];
            $corporate_name = $modulesData['corpName'];

            // ✅ Get all available modules/submodules
            $response2 = $this->httpClient->request('GET', 'V1/corporate/corporate-components/getAllComponents', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);

            $data = $response2['data'];
            $modules = $data['data'] ?? [];

            // ✅ Log modules for debugging
            Log::info('Response from editComponents:', ['response' => $modules]);

            // ✅ Extract forms for module_id = 4 from static list of modules
            $corporateForms = [];
            foreach ($modules as $mod) {
                if ($mod['module_id'] == 4 && !empty($mod['sub_modules'])) {
                    $corporateForms = $mod['sub_modules'];
                    break;
                }
            }

            // ✅ Optional: Log actual selected components
            Log::info('Components for corporate ID ' . $corporate_id, $components);

            return view('content.corporate_list.corporate.components.edit', compact(
                'id',
                'corporate_name',
                'corporate_id',
                'components',
                'modules',
                'componentEditId',
                'corporateForms'
            ));

        } catch (\Exception $e) {
            Log::error('Error fetching component data: ' . $e->getMessage());
            return redirect()->route('corporate-list')->with('error', 'Failed to fetch component data.');
        }
    }

    public function updateComponents(Request $request)
    {
        Log::info('Request Data:', $request->all());
        // return $request->all();
        $componentEditId = Session::get('component_edit_id');
        $validated = $request->validate([
            'module_id' => 'required|array',
            'module_id.*' => 'integer',
            'sub_module_id' => 'nullable|array',
            'sub_module_id.*' => 'nullable|array',
           'sub_module_id.*.*' => 'nullable|integer',
        ]);
        Log::info('Here 2');
        $transformedData = [];
        foreach ($validated['module_id'] as $moduleId) {
            $subModules = $validated['sub_module_id'][$moduleId] ?? [];
            $transformedData[] = [
                'module_id' => $moduleId,
                'sub_module_id' => $subModules,
            ];
        }
        try {
            $response = $this->httpClient->request('POST', 'V1/corporate/corporate-components/updateComponents', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
                'json' => [
                    'corporate_id' => $componentEditId,
                    'components' => $transformedData,
                ],
            ]);
            Log::info('Response from updateComponents:', ['response' => $response]);
            if ($response['success']) {
                return redirect()->route('corporate-list')->with('success', 'Corporate components updated successfully!');
            } else {
                return redirect()->route('corporate-list')->with('error', 'Failed to update components');
            }
        } catch (\Exception $e) {
            return redirect()->route('corporate-list')->with('error', 'Failed to update components.');
        }
    }
    public function assignForms($corporate_id, $location_id)
    {

        return view('content.corporate_list.corporate.forms.edit', [
         'corporate_id' => $corporate_id,
         'location_id' => $location_id
    ]);

    }
    public function getassignedFormForLocation(Request $request, $corporate_id, $location_id)
    {
        //return 'Hi';
        try {
            $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $request->cookie('access_token'),
        ])->get("https://api-admin.hygeiaes.com/V1/corporate/corporate-components/getAssignedForms/{$corporate_id}/{$location_id}");


            if ($response->status() === 401) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            if ($response->successful()) {
                $responseData = $response->json(); // Decode as array
                return response()->json([
                    'success' => true,
                    'data'    => $responseData['data'] ?? [],
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json()['message'] ?? 'Unexpected error',
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getModule4Submodules(Request $request)
    {

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->get('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/getModule4Submodules');

            if ($response->status() === 401) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            if ($response->successful()) {
                $responseData = $response->json(); // Decode as array
                return response()->json([
                    'success' => true,
                    'data'    => $responseData['data'] ?? [],
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json()['message'] ?? 'Unexpected error',
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function assignFormForLocation(Request $request)
    {

        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'corporate_id' => 'required|string|max:255',
            'location_id' => 'required|string|max:255',
            'form_ids' => 'required|array|min:1',
            'form_ids.*' => 'numeric', // Each form ID must be numeric
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // At this point, validation passed, so you can now send the API request
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $request->cookie('access_token'),
            ])->post('https://api-admin.hygeiaes.com/V1/corporate/corporate-components/assignFormForLocation', [
                'corporate_id' => $request->input('corporate_id'),
                'location_id' => $request->input('location_id'),
                'form_ids' => $request->input('form_ids'),
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    return back()->with('success', 'Form Assigned successfully!');
                }
                return back()->with('error', $data['message'] ?? 'Failed to assign forms');
            }

            return back()->with('error', 'Failed to connect to the server');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while assigning the form');
        }
    }

}
