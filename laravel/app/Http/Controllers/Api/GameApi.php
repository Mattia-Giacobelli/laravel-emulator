<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GameApi extends Controller
{
    /**
     * Ottiene le informazioni di una ROM con URL per streaming
     */
    public function getGameInfo(Game $game): JsonResponse
    {
        if (!$game->existsInStorage()) {
            return response()->json([
                'success' => false,
                'message' => 'File non trovato o file corrotto'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $game->toArray(),
            'stream_url' => '/storage/' . $game->file_path,
            'download_url' => url('/storage/' . $game->file_path)
        ]);
    }

    /**
     * Lista tutte le ROM disponibili con metadati aggiuntivi
     */
    public function listGames(): JsonResponse
    {
        $games = Game::select(
            'id',
            'title',
            'region',
            'category',
            'description',
            'file_path',
            'created_at'
        )
            ->orderBy('created_at', 'desc')
            ->get();

        $response = [
            'success' => true,
            'count' => $games->count(),
            'data' => $games->map(function ($game) {
                return [
                    'id' => $game->id,
                    'title' => $game->title,
                    'region' => $game->region ?? 'unknown',
                    'category' => $game->category ?? 'unknown',
                    'description' => $game->description ?? '',
                    'file_path' => $game->file_path
                ];
            })
        ];

        return response()->json($response);
    }

    /**
     * Ottiene lista ROM per categoria
     */
    public function listByCategory(string $category): JsonResponse
    {
        $games = Game::where('category', $category)
            ->orderBy('title', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'category' => $category,
            'count' => $games->count(),
            'data' => $games->map(function ($game) {
                return [
                    'id' => $game->id,
                    'title' => $game->title,
                    'file_path' => $game->file_path
                ];
            })
        ]);
    }

    /**
     * Ottiene lista ROM per regione
     */
    public function listByRegion(string $region): JsonResponse
    {
        $games = Game::where('region', $region)
            ->orderBy('title', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'region' => $region,
            'count' => $games->count(),
            'data' => $games->map(function ($game) {
                return [
                    'id' => $game->id,
                    'title' => $game->title,
                    'file_path' => $game->file_path
                ];
            })
        ]);
    }

    /**
     * Ottiene informazioni sulla dimensione del file
     */
    public function getFileSize(Game $game): JsonResponse
    {
        if (!$game->existsInStorage()) {
            return response()->json([
                'success' => false,
                'message' => 'File non trovato'
            ], 404);
        }

        $path = config('app.storage_path') . '/roms/' . $game->file_path;

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File non esiste fisicamente'
            ], 404);
        }

        $size = filesize($path);

        return response()->json([
            'success' => true,
            'file_size_bytes' => $size,
            'file_size_human' => round($size / 1024, 2) . ' KB',
            'title' => $game->title
        ]);
    }

    /**
     * Ottiene lista degli ultimi file caricati
     */
    public function getRecentGames(int $limit = 10): JsonResponse
    {
        $games = Game::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'count' => $games->count(),
            'data' => $games->map(function ($game) {
                return [
                    'id' => $game->id,
                    'title' => $game->title,
                    'created_at' => $game->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Ottiene statistiche generali delle ROM
     */
    public function getStats(): JsonResponse
    {
        $totalGames = Game::count();
        $totalSize = 0; // Da calcolare con query più complessa

        $byCategory = Game::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->get()
            ->toArray();

        $byRegion = Game::selectRaw('region, count(*) as count')
            ->groupBy('region')
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'total_games' => $totalGames,
            'categories' => $byCategory,
            'regions' => $byRegion
        ]);
    }
}
