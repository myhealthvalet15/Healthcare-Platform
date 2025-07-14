<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShareMenuData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log::info("I am middleware running ...");
        // Check if menu data is cached
        $menuData = Cache::get('menuData');

        if (!$menuData) { 
            // Load the menu data from the JSON files if not cached
            $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
            $verticalMenuData = json_decode($verticalMenuJson);

            $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
            $horizontalMenuData = json_decode($horizontalMenuJson);

            $menuData = [$verticalMenuData, $horizontalMenuData];
            // Cache the menu data for 24 hours
            Cache::put('menuData', $menuData, 60 * 24);
        }
        // Share the menu data globally
        View::share('menuData', $menuData);
        return $next($request);
    }
}
