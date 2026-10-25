<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectRomFilesMiddleware
{
    /**
     * Gestisce la protezione dei file .rom da accesso diretto.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $filePath = $request->getPathInfo();

        // Controlla se la richiesta è per una cartella /storage/
        if (str_contains($filePath, '/storage/')) {
            $filePath = str_replace('/storage/', '', $filePath);

            // Se il percorso contiene .rom o è un file di gioco
            if (preg_match('/\.(nes|gb|gbc|nds|swc|iso|v64)/', $filePath)) {
                // Permetti solo GET/HEAD per streaming
                if (!in_array($request->getMethod(), ['GET', 'HEAD'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accesso non autorizzato',
                        'path' => $filePath
                    ], 403);
                }

                // Continua con la richiesta originale
                return $next($request);
            }
        }

        return $next($request);
    }
}
