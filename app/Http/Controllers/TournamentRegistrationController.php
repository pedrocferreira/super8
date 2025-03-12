<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TournamentRegistrationController extends Controller
{
    public function showRegistration($code)
    {
        $tournament = Tournament::where('registration_code', $code)
            ->where('registration_open', true)
            ->firstOrFail();

        return view('tournaments.register', compact('tournament'));
    }

    public function register(Request $request, $code)
    {
        $tournament = Tournament::where('registration_code', $code)
            ->where('registration_open', true)
            ->firstOrFail();

        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required_if:is_new,true|string|max:255',
            'phone' => 'required_if:is_new,true|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Verifica se o jogador já existe
            $player = Player::where('email', $validated['email'])->first();

            if (!$player && $request->has('is_new')) {
                // Cria novo jogador
                $player = Player::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);
            } elseif (!$player) {
                return response()->json([
                    'status' => 'new_player',
                    'message' => 'Email não encontrado. Por favor, complete seu cadastro.'
                ]);
            }

            // Verifica se já está inscrito
            if ($tournament->players()->where('player_id', $player->id)->exists()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Você já está inscrito neste torneio!'
                ]);
            }

            // Verifica se ainda há vagas
            $currentPlayers = $tournament->players()->count();
            if ($currentPlayers >= $tournament->max_players) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Desculpe, o torneio já está com todas as vagas preenchidas.'
                ]);
            }

            // Registra o jogador no torneio
            $tournament->players()->attach($player->id);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Inscrição realizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao processar sua inscrição. Por favor, tente novamente.'
            ], 500);
        }
    }
} 