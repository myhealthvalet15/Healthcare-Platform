<?php

namespace App\Http\Controllers\injury;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GuzzleHttpClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InjuryController extends Controller
{
    protected $httpClient;
    public function __construct(GuzzleHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    public function index(Request $request)
    {
        try {
            $response = $this->httpClient->request('POST', 'api/injury/index', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                ],
            ]);
            $data = $response['data'];
            $injuries = [
                1 => collect($data['data']['injury_1'] ?? []),
                2 => collect($data['data']['injury_2'] ?? []),
                3 => collect($data['data']['injury_3'] ?? []),
                4 => collect($data['data']['injury_4'] ?? []),
                5 => collect($data['data']['injury_5'] ?? []),
                6 => collect($data['data']['injury_6'] ?? []),
                7 => collect($data['data']['injury_7'] ?? []),
                8 => collect($data['data']['injury_8'] ?? []),
                9 => collect($data['data']['injury_99'] ?? []),
            ];
            foreach ($injuries as $key => $collection) {
                $injuries[$key] = $this->paginate($collection);
            }
            if ($request->ajax()) {
                $injuryKey = $request->input('injury_key');
                if (isset($injuries[$injuryKey])) {
                    $injuryCollection = $injuries[$injuryKey];
                    return response()->json([
                        'data' => view('content.injury.injury_' . $injuryKey, ['injuries' => $injuryCollection])->render(),
                        'pagination' => $injuryCollection->links()->render(),
                    ]);
                }
            }
            return view('content.injury.index', [
                'injuries' => $injuries,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching injuries data', ['error' => $e->getMessage()]);
            return redirect()->route('/')->with('error', 'An error occurred while fetching injury data.');
        }
    }
    private function paginate($collection, $perPage = 25)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        return new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => url()->current()]
        );
    }
    public function create(Request $request)
    {
        // Log::info($request->all());
        try {
            // Log::info('Request data:', $request->all());
            $request->validate([
                'op_component_name' => 'required|string|max:255',
                'op_component_type' => 'required',
                'active_status' => 'required',
            ]);
            $response = $this->httpClient->request('POST', '/api/injury/add', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'op_component_name' => $request->input('op_component_name'),
                    'op_component_type' => $request->input('op_component_type'),
                    'active_status' => $request->input('active_status'),
                ],
            ]);
            return response()->json(['message' => 'Injury added successfully!']);
        } catch (\Exception $e) {
            Log::error('Error creating injury: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create injury.'], 500);
        }
    }
    public function update(Request $request)
    {
        // Log::info($request->all());
        try {
            // Log::info('Request data:', $request->all());
            $request->validate([
                'op_component_name' => 'required|string|max:255',
                'op_component_type' => 'required',
                'op_component_id' => 'required',
            ]);
            $op_component_id = $request->op_component_id;
            $response = $this->httpClient->request('POST', "/api/injury/update/{$op_component_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'op_component_name' => $request->input('op_component_name'),
                    'op_component_type' => $request->input('op_component_type'),
                ],
            ]);
            return response()->json(['message' => 'Injury Updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Error creating injury: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update injury.'], 500);
        }
    }
    public function destroy(Request $request, $id)
    {
        try {
            $op_component_id = $id;
            $response = $this->httpClient->request('DELETE', "/api/injury/delete/{$op_component_id}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->cookie('access_token'),
                    'Content-Type' => 'application/json',
                ],
            ]);
            if ($response['success']) {
                return redirect()->route('outpatient-injury')->with('success', 'Injury deleted successfully!');
            } else {
                return redirect()->route('outpatient-injury')->with('error', 'Failed to delete injury. Status code: ');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting injury: ' . $e->getMessage());
            return redirect()->route('outpatient-injury')->with('error', 'Failed to delete injury due to an internal error.');
        }
    }
}
