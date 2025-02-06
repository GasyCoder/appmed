<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDocumentAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifiez si l'utilisateur a accès au document
        $path = str_replace('/storage/', '', $request->path());

        if (!auth()->user() || !auth()->user()->canAccessDocument($path)) {
            abort(403);
        }

        return $next($request);
    }
}
