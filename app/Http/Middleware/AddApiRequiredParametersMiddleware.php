<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddApiRequiredParametersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->missing('X-Requested-With')) {
            $request->headers->add([
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        }
        return $next($request);
    }
}
