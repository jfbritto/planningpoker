<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Story;
use App\Models\Participant;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    public function store(Request $request, $roomCode, $storyId)
    {
        try {
            $request->validate([
                'value' => 'required|string',
            ]);

            $room = Room::where('code', $roomCode)->firstOrFail();
            
            // Verificar se a sala está ativa
            if (!$room->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta sala não está mais ativa.',
                    'inactive' => true,
                ], 403);
            }
            
            $story = $room->stories()->findOrFail($storyId);
            $sessionId = Session::getId();

            // Buscar participante por session_id
            $participant = $room->participants()
                ->where('session_id', $sessionId)
                ->first();

            // Se não encontrou por session_id, tentar encontrar por cookie (fallback para sessão que mudou)
            if (!$participant) {
                $savedName = $request->cookie('participant_name');
                if ($savedName) {
                    // Tentar encontrar participante com mesmo nome ativo recentemente
                    $participant = $room->participants()
                        ->where('name', $savedName)
                        ->where('last_activity', '>=', now()->subMinutes(5))
                        ->orderBy('last_activity', 'desc')
                        ->first();
                    
                    if ($participant) {
                        // Atualizar session_id para a sessão atual
                        $participant->update([
                            'session_id' => $sessionId,
                            'last_activity' => now(),
                        ]);
                    }
                }
            }

            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Participante não encontrado. Por favor, entre na sala novamente.',
                ], 404);
            }


            $vote = Vote::updateOrCreate(
                [
                    'story_id' => $storyId,
                    'participant_id' => $participant->id,
                ],
                [
                    'value' => $request->value,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Voto registrado com sucesso!',
                'vote' => [
                    'value' => $vote->value,
                    'participant_id' => $participant->id,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos: ' . $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao registrar voto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar voto: ' . $e->getMessage(),
            ], 500);
        }
    }
}

