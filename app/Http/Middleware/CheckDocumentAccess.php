<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckDocumentAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if (!$user || !$user->roles->contains('name', 'teacher')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Permission denied',
                    'message' => 'Accès réservé aux enseignants'
                ], 403);
            }
            
            return redirect()->route('login')->with('error', 'Accès réservé aux enseignants');
        }

        try {
            // Pour la route d'upload des documents
            if ($request->routeIs('document.upload')) {
                return $next($request);
            }

            // Pour les autres routes de documents
            if ($request->routeIs('document.*')) {
                return $next($request);
            }

            // Pour les fichiers dans storage
            if (str_contains($request->path(), 'documents/')) {
                $path = 'documents/' . basename($request->path());
                
                $hasAccess = Document::where('file_path', $path)
                    ->where(function($query) use ($user) {
                        $query->where('uploaded_by', $user->id)
                            ->orWhereIn('niveau_id', $user->teacherNiveaux->pluck('id'));
                    })
                    ->exists();
                    
                if ($hasAccess) {
                    return $next($request);
                }

                throw new \Exception('Accès au document non autorisé');
            }

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Document access error', [
                'user' => $user->id,
                'path' => $request->path(),
                'error' => $e->getMessage(),
                'route' => $request->route()->getName()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Permission denied',
                    'message' => $e->getMessage()
                ], 403);
            }

            return back()->with('error', 'Accès non autorisé');
        }
    }
}