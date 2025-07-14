<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Corporate\EmployeeUserMapping;

class ValidateEmployeeRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('employee_api')->user();
        // This is not required as the auth guard is already set to 'api' in the routes
        // if (! $user || $user->getTable() !== 'corporate_admin_user') {
        //     return response()->json([
        //         'result' => false,
        //         'data' => 'Invalid User'
        //     ], 403);
        // }
        $user = EmployeeUserMapping::where('user_id', $user->user_id)
            ->get();
        if ($user->isEmpty()) {
            return response()->json(['result' => false, 'message' => 'User not found'], 404);
        }
        if (! $user[0]->corporate_id || ! $user[0]->location_id || ! $user[0]->designation || ! $user[0]->employee_type_id || ! $user[0]->hl1_id) {
            return response()->json([
                'result' => false,
                'data' => 'Invalid Request'
            ], 403);
        }
        $request->merge([
            'corporate_id' => $user[0]->corporate_id,
            'location_id' => $user[0]->location_id,
            'designation' => $user[0]->designation,
            'employee_type_id' => $user[0]->employee_type_id,
            'user_id' => $user[0]->user_id,
            'department' => $user[0]->hl1_id,
        ]);

        return $next($request);
    }
}
