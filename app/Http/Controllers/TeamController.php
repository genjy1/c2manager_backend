<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TeamController extends Controller
{
    /**
     * Display a listing of teams.
     */
    public function index(): JsonResponse
    {
        try {
            $teams = Team::with('activePlayers')->get();

            return response()->json([
                'success' => true,
                'data' => $teams
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve teams', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve teams'
            ], 500);
        }
    }

    /**
     * Store a newly created team.
     */
    public function store(StoreTeamRequest $request): JsonResponse
    {
        try {
            $team = DB::transaction(function () use ($request) {
                return Team::create($request->validated());
            });

            return response()->json([
                'success' => true,
                'message' => 'Team created successfully',
                'data' => $team
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create team', [
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create team'
            ], 500);
        }
    }

    /**
     * Display the specified team.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $team = Team::with(['activePlayers', 'captain'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $team
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve team', [
                'team_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team'
            ], 500);
        }
    }

    /**
     * Update the specified team.
     */
    public function update(UpdateTeamRequest $request, int $id): JsonResponse
    {
        try {
            $team = Team::findOrFail($id);

            DB::transaction(function () use ($team, $request) {
                $team->update($request->validated());
            });

            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully',
                'data' => $team->fresh()
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update team', [
                'team_id' => $id,
                'error' => $e->getMessage(),
                'request_data' => $request->validated()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update team'
            ], 500);
        }
    }

    /**
     * Remove the specified team from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $team = Team::findOrFail($id);

            DB::transaction(function () use ($team) {
                // Pivot relationships will be deleted automatically due to cascade
                $team->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Team deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete team', [
                'team_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team'
            ], 500);
        }
    }

    /**
     * Add a player to the team.
     */
    public function addPlayer(Request $request, int $teamId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'player_id' => 'required|exists:players,id',
                'position' => 'nullable|string|max:255',
                'joined_at' => 'nullable|date',
                'is_captain' => 'nullable|boolean',
            ]);

            $team = Team::findOrFail($teamId);

            DB::transaction(function () use ($team, $validated) {
                // Check if player is already in the team
                $existingPivot = $team->players()
                    ->wherePivot('player_id', $validated['player_id'])
                    ->whereNull('player_team.left_at')
                    ->exists();

                if ($existingPivot) {
                    throw ValidationException::withMessages([
                        'player_id' => ['Player is already in this team']
                    ]);
                }

                $team->players()->attach($validated['player_id'], [
                    'position' => $validated['position'] ?? null,
                    'joined_at' => $validated['joined_at'] ?? now(),
                    'is_captain' => $validated['is_captain'] ?? false,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Player added to team successfully'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to add player to team', [
                'team_id' => $teamId,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add player to team'
            ], 500);
        }
    }

    /**
     * Remove a player from the team.
     */
    public function removePlayer(Request $request, int $teamId, int $playerId): JsonResponse
    {
        try {
            $team = Team::findOrFail($teamId);

            DB::transaction(function () use ($team, $playerId) {
                // Set left_at date instead of detaching
                $team->players()
                    ->wherePivot('player_id', $playerId)
                    ->whereNull('player_team.left_at')
                    ->updateExistingPivot($playerId, [
                        'left_at' => now()
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Player removed from team successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to remove player from team', [
                'team_id' => $teamId,
                'player_id' => $playerId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove player from team'
            ], 500);
        }
    }
}
