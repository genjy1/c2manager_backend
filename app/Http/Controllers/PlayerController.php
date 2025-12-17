<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Models\Player;
use App\Models\PlayerImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayerController extends Controller
{
    /**
     * Display a listing of players.
     */
    public function index(): JsonResponse
    {
        try {
            $players = Player::with('images')->get();

            return response()->json([
                'success' => true,
                'data' => $players
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve players', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve players'
            ], 500);
        }
    }

    /**
     * Store a newly created player with optional avatar.
     */
    public function store(StorePlayerRequest $request): JsonResponse
    {
        try {
            $player = DB::transaction(function () use ($request) {
                // Create player
                $player = Player::create([
                    'name' => $request->input('name'),
                    'surname' => $request->input('surname'),
                    'nickname' => $request->input('nickname'),
                    'rating' => $request->input('rating'),
                ]);

                // Create player image if avatar is provided
                if ($request->filled('avatar')) {
                    PlayerImage::create([
                        'player_id' => $player->id,
                        'base64' => $request->input('avatar'),
                        'mime_type' => $request->input('mime_type'),
                    ]);
                }

                return $player;
            });

            // Reload with relationships
            $player->load('images');

            return response()->json([
                'success' => true,
                'message' => 'Player created successfully',
                'data' => $player
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create player', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['avatar']) // Don't log base64
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create player'
            ], 500);
        }
    }

    /**
     * Remove the specified player from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $player = Player::findOrFail($id);

            DB::transaction(function () use ($player) {
                // Images will be deleted automatically due to cascade delete
                $player->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Player deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Player not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete player', [
                'player_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete player'
            ], 500);
        }
    }
}
