<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\Hra\Master_Tests\MasterTest;

class CorporateStubs extends Controller
{
    public function getAllCertificates($corporateId)
    {
        // TODO: Check the incoming corporate id with the logged in users corporate id
        if (empty($corporateId)) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid Corporate Id'
            ], 400);
        }
        $certificates = Certification::where('corporate_id', $corporateId)->get();
        if ($certificates->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => 'No certificates found.'
            ], 200);
        }
        return response()->json([
            'result' => true,
            'total_count' => $certificates->count(),
            'certificates' => $certificates
        ]);
    }

    public function getAllTests()
    {
        try {
            $tests = MasterTest::all();
            return response()->json(['data' => $tests], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
