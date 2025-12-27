<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDocumentAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $document = $request->route('document');
        if (!$document) abort(404, 'Document introuvable (route-model binding)');

        if (!$document->canAccess($user)) {
            abort(403, "Vous n'avez pas accès à ce document");
        }

        return $next($request);
    }
}
