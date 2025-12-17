<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerImage;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    // Получение списка игроков
    public function index()
    {
        $players = Player::with('images')->get();
        return response()->json(['data' => $players], 200);
    }

    // Создание нового игрока с аватаром
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'rating' => 'required|numeric|min:0',
            'avatar' => 'nullable|string', // base64
        ]);

        $player = Player::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'nickname' => $request->nickname,
            'rating' => $request->rating,
        ]);

        if (!empty($request->input('avatar')) && $request->input('avatar') != null) {
            PlayerImage::create([
                'player_id' => $player->id,
                'base64' => $request->avatar,
                'mime' => $request->mime,
            ]);
        }



        return response()->json([
            'success' => true,
            'player' => $player->load('images')
        ]);
    }

    public function destroy(int $id)
    {
        $player = Player::find($id);

        if (!$player) {
            return response()->json([
                'message' => 'Player not found'
            ], 404);
        }

        $player->delete();

        return response()->json([
            'success' => true,
            'data' => Player::with('images')->get()
        ], 200);
    }

}
