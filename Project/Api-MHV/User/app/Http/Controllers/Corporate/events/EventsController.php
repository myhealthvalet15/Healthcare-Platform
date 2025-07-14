<?php 
namespace App\Http\Controllers\corporate\events;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Corporate\Event;
use App\Models\Corporate\EventDetails;
use App\Models\Department\CorporateHl1;
use App\Models\Employee\EmployeeType;
use App\Models\PrescribedTest;
use Illuminate\Support\Facades\Log;
use App\Models\TestGroup;
use App\Models\Hra\Master_Tests\MasterTest;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\MailController;

class EventsController extends Controller
{
  public function addEvents(Request $request, $corporate_id, $location_id)
{
   // $corporate_id= $request->corporate_id;
    $validator = Validator::make($request->all(), [
        'event_name'        => 'required|string|max:255',
        'event_description' => 'nullable|string',
        'guest_name'        => 'required|string|max:255',
        'from_date'         => 'required|date_format:Y-m-d H:i',
        'to_date'           => 'required|date_format:Y-m-d H:i|after:from_date',
        'department'        => 'required|array',
        'department.*'      => 'integer',
        'employee_type'     => 'required|array',
        'employee_type.*'   => 'integer',
        'test'              => 'required|array',
        'test.*'            => 'integer',
    ]);

    if ($validator->fails()) {
        return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
    }

    $validated = $validator->validated();

    // Save event
    $event = Event::create([
        'corporate_id'      => $corporate_id,
        'event_name'        => $validated['event_name'],
        'event_description' => $validated['event_description'] ?? null,
        'guest_name'        => $validated['guest_name'],
        'from_datetime'     => $validated['from_date'],
        'to_datetime'       => $validated['to_date'],
    ]);

    // Save event detail
    $eventDetail = EventDetails::create([
        'corporate_id'  => $corporate_id,
        'event_row_id'  => $event->event_id,
        'employee_type' => $validated['employee_type'],
        'department'    => $validated['department'],
        'test_taken'    => $validated['test'],
        'condition'     => null,
    ]);

    // Prepare email content
    $subject   = "New Event Created: " . $validated['event_name'];
    $toEmail   = 'bhavawebcoder@gmail.com';
    $emailType = 'event_notification';

    $body = view('email.event_notification', [
        'event_name'        => $validated['event_name'],
        'event_description' => $validated['event_description'] ?? '',
        'guest_name'        => $validated['guest_name'],
        'from_date'         => $validated['from_date'],
        'to_date'           => $validated['to_date'],
        'department_names'  => CorporateHl1::whereIn('hl1_id', $validated['department'])->pluck('hl1_name')->toArray(),
        'employee_type_names' => EmployeeType::whereIn('employee_type_id', $validated['employee_type'])->pluck('employee_type_name')->toArray(),
       // 'test_names'        => PrescribedTest::whereIn('prescribed_test_id', $validated['test'])->pluck('prescribed_test_name')->toArray(),
    ])->render();
    // Redirect to preview route instead of sending email
   return redirect()->route('email.preview', [
    'subject'    => $subject,
    'toEmail'    => $toEmail,
    'emailType'  => $emailType,
    'body'       => null, // Let the preview build this
    'data'       => json_encode([
        'event_name'        => $validated['event_name'],
        'event_description' => $validated['event_description'] ?? '',
        'guest_name'        => $validated['guest_name'],
        'from_date'         => $validated['from_date'],
        'to_date'           => $validated['to_date'],
        'department_names'  => CorporateHl1::whereIn('hl1_id', $validated['department'])->pluck('hl1_name')->toArray(),
        'employee_type_names' => EmployeeType::whereIn('employee_type_id', $validated['employee_type'])->pluck('employee_type_name')->toArray(),
        'header_title'      => 'Event Notification',
    ])
]);

}


public function listEventsBYPostman()
{
 return response()->json([
        'result' => true,
        'message' => 'This is a test response from the listEventsBYPostman method.'
    ], 200);
}

public function getAllEventsByCorporate($corporate_id)
{
   $events = Event::with('details')
    ->where('corporate_id', $corporate_id)
    ->orderByDesc('event_id') 
    ->get();

    if ($events->isEmpty()) {
        return response()->json(['result' => true, 'data' => $events], 200);
    }

    foreach ($events as $event) {
        if ($event->details) {
           
            $departmentIds = is_array($event->details->department) ? $event->details->department : [];
            $departments = CorporateHl1::whereIn('hl1_id', $departmentIds)->get(['hl1_id', 'hl1_name']);
            $event->details->department_names = $departments->pluck('hl1_name', 'hl1_id');

           
            $employeeTypeIds = is_array($event->details->employee_type) ? $event->details->employee_type : [];
            $employeeTypes = EmployeeType::whereIn('employee_type_id', $employeeTypeIds)->get(['employee_type_id', 'employee_type_name']);
            $event->details->employee_type_names = $employeeTypes->pluck('employee_type_name', 'employee_type_id');

            $testIds = is_array($event->details->test_taken) ? $event->details->test_taken : [];
            $tests = MasterTest::whereIn('master_test_id', $testIds)->get(['master_test_id', 'test_name']);
            $event->details->test_names = $tests->pluck('test_name', 'master_test_id');
        }
    }

    return response()->json([
        'result' => true,
        'data' => $events
    ], 200);
}
public function destroyEvents($id)
{
    try {
        DB::beginTransaction();

        // Find event
        $event = Event::findOrFail($id);

        // Delete associated event details
        EventDetails::where('event_row_id', $event->event_id)->delete();

        // Delete the event
        $event->delete();

        DB::commit();

        return response()->json(['message' => 'Event and its details deleted successfully.'], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error deleting event: ' . $e->getMessage());

        return response()->json(['message' => 'Failed to delete event.'], 500);
    }
}
public function editEventsById($id, $corporate_id)
{
    try {
        $event = Event::with('details')->where('event_id', $id)->where('corporate_id', $corporate_id)->first();

        if (!$event) {
            return response()->json(['result' => false, 'message' => 'Event not found'], 404);
        }
        $departments = CorporateHl1::all();
        $employeeTypes = EmployeeType::all();
        $tests = MasterTest::all();
        return response()->json([
            'result' => true,
            'data' => [
                'event' => $event,
                'departments' => $departments,
                'employeeTypes' => $employeeTypes,
                'tests' => $tests
            ]
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error fetching event by ID: ' . $e->getMessage());
        return response()->json(['result' => false, 'message' => 'Internal Server Error'], 500);
    }


}
public function updateEventsById(Request $request, $id)
{
    //Log::info('Incoming request:', $request->all());
    //return $request->all();


    try {
        $validator = Validator::make($request->all(), [
            'event_name'        => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'guest_name'        => 'required|string|max:255',
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s|after:from_date',

            'department'        => 'required|array',
            'department.*'      => 'integer',
            'employee_type'     => 'required|array',
            'employee_type.*'   => 'integer',
            'test'              => 'required|array',
            'test.*'            => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Find the event
        $event = Event::where('event_id', $id)->first();
        if (!$event) {
            return response()->json(['result' => false, 'message' => 'Event not found'], 404);
        }

        // Update event
        $event->update([
            'event_name'        => $validated['event_name'],
            'event_description' => $validated['event_description'] ?? null,
            'guest_name'        => $validated['guest_name'],
            'from_datetime'     => $validated['from_date'],
            'to_datetime'       => $validated['to_date'],
        ]);

        // Update event details
        $eventDetail = EventDetails::updateOrCreate(
            ['event_row_id' => $event->event_id],
            [
                'employee_type' => $validated['employee_type'],
                'department'    => $validated['department'],
                'test_taken'    => $validated['test'],
                'condition'     => null,
            ]
        );

        return response()->json(['result' => true, 'message' => 'Event updated successfully'], 200);
    } catch (\Exception $e) {
        Log::error('Error updating event: ');
   }  
}
}