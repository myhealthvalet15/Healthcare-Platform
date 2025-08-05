<?php

namespace App\Http\Controllers\Corporate\reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Corporate\EmployeeUserMapping;
use App\Models\Hra\Master_Tests\MasterTest;
use App\Models\Certification;
use App\Models\TestGroup;
use App\Models\Department\CorporateHl1;
use App\Models\Employee\EmployeeType;
use App\Models\PrescribedTest;
use App\Models\PrescribedTestData;
use App\Models\Corporate\MasterUser;



use Carbon\Carbon;

class MhcReportsController extends Controller
{
  
   public function getEmployeeHealthData($location_id, $corporate_id, Request $request)
{
    $validated = $request->validate([
        'employeeType'     => 'nullable|integer',
        'medicalCondition' => 'nullable|string',
        'department'       => 'nullable|integer',
        'ageGroup'         => 'nullable|string',
    ]);

    $query = PrescribedTest::with([
        'user.employeeType',
        'user.corporateHL1',
        'user.masterUser',
        'masterTest'
     ])
    ->where('location_id', $location_id)
    ->where('corporate_id', $corporate_id);

    if (!empty($validated['employeeType'])) {
        $query->whereHas('user', fn($q) => $q->where('employee_type_id', $validated['employeeType']));
    }

    if (!empty($validated['department'])) {
        $query->whereHas('user', fn($q) => $q->where('hl1_id', $validated['department']));
    }

    if (!empty($validated['ageGroup'])) {
        [$minAge, $maxAge] = explode(' and ', $validated['ageGroup']);
        $query->whereHas('user.masterUser', fn($q) =>
            $q->whereRaw("TIMESTAMPDIFF(YEAR, dob, CURDATE()) BETWEEN ? AND ?", [(float)$minAge, (float)$maxAge])
        );
    }

    if (!empty($validated['medicalCondition'])) {
        $query->where('test_code', $validated['medicalCondition']);
    }

    $prescribedTests = $query->get();

    $testCodes = $prescribedTests->pluck('test_code')->unique()->toArray();
    $userIds = $prescribedTests->pluck('user_id')->unique()->toArray();

    $testResults = PrescribedTestData::whereIn('test_code', $testCodes)
        ->whereIn('user_id', $userIds)
        ->get()
        ->groupBy(fn($item) => $item->test_code . '-' . $item->user_id);

    $groupedData = [];

    foreach ($prescribedTests as $test) {
        $user = $test->user;
        $master = $test->masterTest;
        if (!$user || !$user->masterUser || !$master) continue;

        $dob = $user->masterUser->dob;
        if (!$dob) continue;

        $age = Carbon::parse($dob)->age;

        $ageGroup = match (true) {
            $age >= 20 && $age <= 30 => '20-30',
            $age > 30 && $age <= 40 => '30-40',
            $age > 40 && $age <= 50 => '40-50',
            $age > 50 => '50+',
            default => 'Unknown'
        };

        // Match test result
        $key = $test->test_code . '-' . $test->user_id;
        $resultRow = $testResults[$key][0] ?? null;
        if (!$resultRow || !is_numeric($resultRow->test_results)) continue;

        $value = (float) $resultRow->test_results;

        // Parse min/max range
        [$min, $max] = explode('-', $master->m_min_max ?? '0-0');
        $min = (float) $min;
        $max = (float) $max;

        $status = 'Normal';
        if ($value < $min) {
            $status = 'Low';
        } elseif ($value > $max) {
            $status = 'High';
        }

        if (!isset($groupedData[$ageGroup])) {
            $groupedData[$ageGroup] = ['Low' => 0, 'Normal' => 0, 'High' => 0];
        }

        $groupedData[$ageGroup][$status]++;
    }

    return response()->json([
        'result' => true,
        'data' => $groupedData
    ]);
}

}