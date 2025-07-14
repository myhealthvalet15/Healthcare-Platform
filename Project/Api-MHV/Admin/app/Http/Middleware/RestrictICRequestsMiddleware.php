<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictICRequestsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $allowedIps = ['103.101.58.167', '54.86.50.139'];
        if (!in_array($request->ip(), $allowedIps)) {
            return response()->json([
                'result' => false,
                // 'your_ip' => 'Your IP: ' . $request->ip().' 📍',
                'message' => 'Access Denied!🚫, Sorry, kiddo. You’re not on our developer list. Hope you’re not a tester, lol. 😜 But hey, if you’re up for a challenge, crack this security and let me know if you find anything interesting! 🕵️‍♂️. Got something cool to report? Drop me a line at mspraveenkumar77@gmail.com. I’d love to hear from you!',
                'note' => 'Just a heads-up: this is a development server, so don’t expect any treasure here. But kudos for trying!',
                // 'hint' => 'Hint: Maybe try asking nicely next time? 😉🤖'
            ], 403);
        }
        return $next($request);
    }
}
