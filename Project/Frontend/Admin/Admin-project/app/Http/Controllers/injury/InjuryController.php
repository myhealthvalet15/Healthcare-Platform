<?php

namespace App\Http\Controllers\injury;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InjuryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $response = Http::post('https://api-admin.hygeiaes.com/api/injury/index', [
                'access_token' => $request->cookie('access_token'),
            ]);

            $data = $response->json('data');

            $injuries = [
                1 => collect($data['injury_1'] ?? []),
                2 => collect($data['injury_2'] ?? []),
                3 => collect($data['injury_3'] ?? []),
                4 => collect($data['injury_4'] ?? []),
                5 => collect($data['injury_5'] ?? []),
                6 => collect($data['injury_6'] ?? []),
                7 => collect($data['injury_7'] ?? []),
                8 => collect($data['injury_8'] ?? []),
                9 => collect($data['injury_99'] ?? []),
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
            Log::error('Error fetching injuries data', ['error' => $e->getMessage()]);
            return redirect('/')->with('error', 'An error occurred while fetching injury data.');
        }
    }

    public function create(Request $request)
    {
        $request->validate([
            'op_component_name' => 'required|string|max:255',
            'op_component_type' => 'required',
            'active_status' => 'required',
        ]);

        try {
            $response = Http::post('https://api-admin.hygeiaes.com/api/injury/add', [
                'op_component_name' => $request->op_component_name,
                'op_component_type' => $request->op_component_type,
                'active_status' => $request->active_status,
                'access_token' => $request->cookie('access_token'),
            ]);

            return response()->json(['message' => 'Injury added successfully!']);
        } catch (\Exception $e) {
            Log::error('Error creating injury: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create injury.'], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'op_component_name' => 'required|string|max:255',
            'op_component_type' => 'required',
            'op_component_id' => 'required',
        ]);

        try {
            $response = Http::post("https://api-admin.hygeiaes.com/api/injury/update/{$request->op_component_id}", [
                'op_component_name' => $request->op_component_name,
                'op_component_type' => $request->op_component_type,
                'access_token' => $request->cookie('access_token'),
            ]);

            return response()->json(['message' => 'Injury updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Error updating injury: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update injury.'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $response = Http::delete("https://api-admin.hygeiaes.com/api/injury/delete/{$id}", [
                'access_token' => $request->cookie('access_token'),
            ]);

            if ($response->json('success')) {
                return redirect()->route('outpatient-injury')->with('success', 'Injury deleted successfully!');
            } else {
                return redirect()->route('outpatient-injury')->with('error', 'Failed to delete injury.');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting injury: ' . $e->getMessage());
            return redirect()->route('outpatient-injury')->with('error', 'Failed to delete injury due to an internal error.');
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
}
