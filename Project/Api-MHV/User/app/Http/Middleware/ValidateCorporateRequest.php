<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateCorporateRequest
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        // This is not required as the auth guard is already set to 'api' in the routes
        // if (! $user || $user->getTable() !== 'corporate_admin_user') {
        //     return response()->json([
        //         'result' => false,
        //         'data' => 'Invalid User'
        //     ], 403);
        // }

        if (! $user->corporate_id || ! $user->location_id) {
            return response()->json([
                'result' => false,
                'data' => 'Invalid Request'
            ], 403);
        }
        $request->merge([
            'corporate_id' => $user->corporate_id,
            'location_id' => $user->location_id,
        ]);

        return $next($request);
    }
}
