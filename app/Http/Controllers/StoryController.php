<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class StoryController extends Controller
{
    public function startNew(Request $request, $roomCode)
    {
        $room = Room::where('code', $roomCode)->firstOrFail();
        
        // Verificar se a sala está ativa
        if (!$room->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Esta sala não está mais ativa.',
                'inactive' => true,
            ], 403);
        }
        
        $sessionId = Session::getId();
        
        // Verificar se é o criador da sala
        if ($room->creator_session_id !== $sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o criador da sala pode iniciar uma nova estimativa.',
            ], 403);
        }

        // Marcar todas as histórias anteriores como reveladas
        $room->stories()->update(['is_revealed' => true]);

        // Contar quantas histórias já foram criadas
        $storyCount = $room->stories()->count() + 1;

        $story = $room->stories()->create([
            'title' => "Estimativa #{$storyCount}",
            'description' => null,
            'is_revealed' => false,
        ]);

        return response()->json([
            'success' => true,
            'story' => [
                'id' => $story->id,
                'title' => $story->title,
                'is_revealed' => $story->is_revealed,
            ],
        ]);
    }

    public function reveal(Request $request, $roomCode, $storyId)
    {
        $room = Room::where('code', $roomCode)->firstOrFail();
        
        // Verificar se a sala está ativa
        if (!$room->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Esta sala não está mais ativa.',
                'inactive' => true,
            ], 403);
        }
        
        $sessionId = Session::getId();
        
        // Verificar se é o criador da sala
        if ($room->creator_session_id !== $sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o criador da sala pode revelar os votos.',
            ], 403);
        }

        // Tentar até 3 vezes em caso de deadlock
        $maxRetries = 3;
        $retry = 0;
        
        while ($retry < $maxRetries) {
            try {
                return DB::transaction(function () use ($room, $storyId) {
                    // Usar lockForUpdate para evitar condições de corrida
                    $story = $room->stories()
                        ->where('id', $storyId)
                        ->lockForUpdate()
                        ->firstOrFail();
                    
                    // Verificar se já foi revelado (evitar processamento duplicado)
                    if ($story->is_revealed) {
                        $story->load('votes.participant');
                        
                        // Calcular all_same_vote mesmo se já estiver revelado
                        $allSame = $this->checkAllSameVote($story);
                        
                        // Preparar lista de votos formatada para o frontend
                        // Filtrar votos com participante válido e incluir votos órfãos (sem participante)
                        $votes = $story->votes->filter(function($vote) {
                            return $vote->participant !== null;
                        })->map(function($vote) {
                            return [
                                'id' => $vote->participant->id,
                                'name' => $vote->participant->name,
                                'vote_value' => $vote->value,
                            ];
                        })->values()->toArray();
                        
                        // Adicionar votos órfãos (sem participante) com nome genérico
                        $orphanVotes = $story->votes->filter(function($vote) {
                            return $vote->participant === null;
                        })->map(function($vote) {
                            return [
                                'id' => null,
                                'name' => 'Participante Removido',
                                'vote_value' => $vote->value,
                            ];
                        })->values()->toArray();
                        
                        $votes = array_merge($votes, $orphanVotes);
                        
                        // Se não houver votos na story, buscar todos os participantes
                        if (empty($votes)) {
                            $participants = $room->participants()->get();
                            $votes = $participants->map(function($participant) {
                                return [
                                    'id' => $participant->id,
                                    'name' => $participant->name,
                                    'vote_value' => null,
                                ];
                            })->values()->toArray();
                        }
                        
                        return response()->json([
                            'success' => true,
                            'story' => [
                                'id' => $story->id,
                                'is_revealed' => $story->is_revealed,
                                'average_vote' => $story->average_vote,
                                'votes_count' => $story->votes->count(),
                            ],
                            'votes' => $votes,
                            'all_same_vote' => $allSame,
                            'is_creator' => true,
                        ]);
                    }
                    
                    // Atualizar status
                    $story->update(['is_revealed' => true]);
                    
                    // Recarregar com votos
                    $story->load('votes.participant');
                    
                    // Verificar se todos votaram o mesmo número
                    $allSame = $this->checkAllSameVote($story);
                    
                    \Log::info('Reveal - all_same_vote calculado', [
                        'story_id' => $story->id,
                        'all_same_vote' => $allSame,
                        'votes_count' => $story->votes->count(),
                    ]);
                    
                    // Preparar lista de votos formatada para o frontend
                    $votes = $story->votes->map(function($vote) {
                        return [
                            'id' => $vote->participant->id,
                            'name' => $vote->participant->name,
                            'vote_value' => $vote->value,
                        ];
                    })->values()->toArray();
                    
                    // Se não houver votos na story, buscar todos os participantes
                    if (empty($votes)) {
                        $participants = $room->participants()->get();
                        $votes = $participants->map(function($participant) {
                            return [
                                'id' => $participant->id,
                                'name' => $participant->name,
                                'vote_value' => null,
                            ];
                        })->values()->toArray();
                    }

                    return response()->json([
                        'success' => true,
                        'story' => [
                            'id' => $story->id,
                            'is_revealed' => $story->is_revealed,
                            'average_vote' => $story->average_vote,
                            'votes_count' => $story->votes->count(),
                        ],
                        'votes' => $votes,
                        'all_same_vote' => $allSame,
                        'is_creator' => true,
                    ]);
                });
            } catch (QueryException $e) {
                // Verificar se é um deadlock
                if ($e->getCode() == 40001 || str_contains($e->getMessage(), 'Deadlock')) {
                    $retry++;
                    
                    if ($retry >= $maxRetries) {
                        \Log::error('Deadlock após múltiplas tentativas', [
                            'room_code' => $roomCode,
                            'story_id' => $storyId,
                            'retries' => $retry,
                        ]);
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Erro ao revelar votos. Por favor, tente novamente.',
                        ], 500);
                    }
                    
                    // Esperar um tempo aleatório antes de tentar novamente (backoff exponencial)
                    usleep(rand(100000, 500000) * $retry); // 100-500ms * retry
                    
                    continue;
                }
                
                // Se não for deadlock, relançar a exceção
                throw $e;
            }
        }
    }
    
    /**
     * Verifica se todos os votos são iguais
     */
    private function checkAllSameVote(Story $story): bool
    {
        $allVotes = $story->votes()->get();
        
        // Se não houver votos, retornar false
        if ($allVotes->isEmpty()) {
            return false;
        }
        
        $numericVotes = $allVotes
            ->filter(function($vote) {
                // Filtrar apenas votos numéricos (não '?', 'coffee', etc)
                return is_numeric($vote->value) && !in_array($vote->value, ['?', 'coffee']);
            })
            ->map(function($vote) {
                // Converter para string para comparação exata
                return (string) $vote->value;
            })
            ->values();
        
        // Precisa ter pelo menos 2 votos numéricos para comparar
        if ($numericVotes->count() < 2) {
            return false;
        }
        
        $firstVote = $numericVotes->first();
        $allSame = $numericVotes->every(function($vote) use ($firstVote) {
            return $vote === $firstVote; // Comparação exata de strings
        });
        
        // Log para debug
        \Log::info('Verificando votos iguais', [
            'total_votes' => $allVotes->count(),
            'numeric_votes_count' => $numericVotes->count(),
            'first_vote' => $firstVote,
            'all_votes' => $numericVotes->toArray(),
            'all_same' => $allSame,
        ]);
        
        return $allSame;
    }
}

