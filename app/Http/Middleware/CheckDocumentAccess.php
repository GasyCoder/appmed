<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckDocumentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $document = $request->route('document');

        if (!$document) {
            abort(404, 'Document introuvable (route-model binding)');
        }

        if (!$document->canAccess($user)) {
            Log::warning("Access denied to document {$document->id} for user {$user->id}");
            abort(403, "Vous n'avez pas accès à ce document");
        }

        Log::info("Access granted to document {$document->id} for user {$user->id}");

        return $next($request);
    }
}
