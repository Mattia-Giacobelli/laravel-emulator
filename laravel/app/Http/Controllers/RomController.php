<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class RomController extends Controller
{
    /**
     * Lista tutte le ROM disponibili
     */
    public function index()
    {
        $games = Game::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $games
        ]);
    }

    /**
     * Restituisce le informazioni di una ROM specifica
     */
    public function show(Game $game): JsonResponse
    {
        if (!$game->existsInStorage()) {
            return response()->json([
                'success' => false,
                'message' => 'File non trovato'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $game->toArray()
        ]);
    }

    /**
     * Carica una nuova ROM nel sistema
     */
    public function store(Request $request): JsonResponse
    {
        // Validazione del file
        $validated = $request->validate([
            'file' => 'required|file|max:50000',
            'title' => 'nullable|string|max:255',
            'region' => 'nullable|in:us,jp,eu,au,korea,asia',
            'category' => 'nullable|in:nintendo-switch,nintendo-64,nintendods,nintendods-cart,dendy,tetris'
        ]);

        // Salva il file nella cartella roms
        $file = $request->file('file');

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Nessun file caricato'
            ], 400);
        }

        // Genera nome univoco per il file
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $newName = md5(uniqid() . '-' . time() . '.' . $extension);

        // Salva nella directory storage/app/public/roms
        $path = Storage::disk('public')->put('roms', fopen($file->getRealPath(), 'r'), $newName);

        if (!$path) {
            return response()->json([
                'success' => false,
                'message' => 'Impossibile salvare il file'
            ], 500);
        }

        // Crea/aggiorna la registrazione nel database
        Game::updateOrCreate(
            [
                'title' => $validated['title'] ?? null
            ],
            [
                'title' => $validated['title'] ?? pathinfo($originalName, PATHINFO_FILENAME),
                'region' => $validated['region'] ?? 'unknown',
                'category' => $validated['category'] ?? 'nintendo-switch',
                'description' => '',
                'file_path' => str_replace('/roms/', '', $path)
            ]
        );

        // Rimuovi il file temporaneo se esiste
        if (file_exists($request->file('file')->getRealPath())) {
            unlink($request->file('file')->getRealPath());
        }

        return response()->json([
            'success' => true,
            'message' => 'ROM caricata con successo',
            'game_id' => Game::where('title', $validated['title'] ?? pathinfo($originalName, PATHINFO_FILENAME))->first()?->id
        ]);
    }

    /**
     * Elimina una ROM dal sistema
     */
    public function destroy(Game $game): JsonResponse
    {
        if (!$game->existsInStorage()) {
            return response()->json([
                'success' => false,
                'message' => 'File non trovato'
            ], 404);
        }

        // Elimina il file fisico
        Storage::disk('public')->delete('roms/' . $game->file_path);

        // Elimina il record dal database
        $game->delete();

        return response()->json([
            'success' => true,
            'message' => 'ROM eliminata con successo'
        ]);
    }
}
