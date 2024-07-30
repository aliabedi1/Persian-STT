<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ValidateSignature
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasValidSignatureWhileIgnoring()) {
            return Response::dataNotFound();
        }
        return $next($request);
    }
}

