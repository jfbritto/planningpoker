<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Story;
use App\Models\Participant;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class RoomController extends Controller
{
    public function index()
    {
        // Limpar salas antigas ou sem participantes antes de listar
        $this->cleanupOldRooms();
        
        // Listar salas ativas ordenadas por última atividade
        // Filtrar salas com mais de 24h ou sem participantes
        $rooms = Room::where('is_active', true)
            ->where('created_at', '>=', now()->subDay()) // Criadas há menos de 24h
            ->withCount('participants')
            ->having('participants_count', '>', 0) // Com pelo menos 1 participante
            ->with('participants')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('welcome', compact('rooms'));
    }

    public function dashboard()
    {
        // Estatísticas gerais
        $totalRooms = Room::count();
        $activeRooms = Room::where('is_active', true)
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $totalParticipants = Participant::count();
        $activeParticipants = Participant::where('last_activity', '>=', now()->subMinutes(10))->count();
        $totalStories = Story::count();
        $totalVotes = Vote::count();
        $revealedStories = Story::where('is_revealed', true)->count();
        
        // Salas recentes (últimas 10)
        $recentRooms = Room::withCount(['participants', 'stories'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Histórico de atividades (últimas 24h)
        $roomsToday = Room::where('created_at', '>=', now()->subDay())->count();
        $participantsToday = Participant::where('created_at', '>=', now()->subDay())->count();
        $votesToday = Vote::where('created_at', '>=', now()->subDay())->count();
        
        // Média de votos por história
        $avgVotesPerStory = $totalStories > 0 ? round($totalVotes / $totalStories, 2) : 0;
        
        // Média de participantes por sala
        $avgParticipantsPerRoom = $totalRooms > 0 ? round($totalParticipants / $totalRooms, 2) : 0;
        
        return view('dashboard', compact(
            'totalRooms',
            'activeRooms',
            'totalParticipants',
            'activeParticipants',
            'totalStories',
            'totalVotes',
            'revealedStories',
            'recentRooms',
            'roomsToday',
            'participantsToday',
            'votesToday',
            'avgVotesPerStory',
            'avgParticipantsPerRoom'
        ));
    }
    
    /**
     * Desativa salas antigas (mais de 24h) ou sem participantes
     * Mantém os dados históricos (participantes, histórias, votos) para estatísticas
     */
    private function cleanupOldRooms()
    {
        // Salas criadas há mais de 24h - apenas desativar, não deletar
        $oldRooms = Room::where('is_active', true)
            ->where('created_at', '<', now()->subDay())
            ->update(['is_active' => false]);
        
        // Salas sem participantes ativos - apenas desativar, não deletar
        $emptyRooms = Room::where('is_active', true)
            ->whereDoesntHave('participants', function($query) {
                $query->where('last_activity', '>=', now()->subMinutes(10));
            })
            ->update(['is_active' => false]);
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'creator_name' => 'required|string|max:255',
        ]);

        $sessionId = Session::getId();
        $creatorName = $request->creator_name;

        $room = Room::create([
            'code' => Room::generateCode(),
            'name' => $request->name,
            'creator_session_id' => $sessionId,
        ]);

        // Criar primeira história automaticamente
        $room->stories()->create([
            'title' => 'Estimativa #1',
            'description' => null,
            'is_revealed' => false,
        ]);

        // Salvar nome do criador em cookie e redirecionar
        return redirect()
            ->route('rooms.show', $room->code)
            ->cookie('participant_name', $creatorName, 60 * 24 * 30); // 30 dias
    }

    public function show(Request $request, $code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $sessionId = Session::getId();
        $isCreator = $room->creator_session_id === $sessionId;
        
        // Buscar participante atual ANTES da limpeza
        $currentParticipant = $room->participants()
            ->where('session_id', $sessionId)
            ->first();
        
        // Se não encontrou por session_id, tentar encontrar por cookie (fallback para sessão que mudou)
        if (!$currentParticipant) {
            $savedName = $request->cookie('participant_name');
            if ($savedName) {
                // Tentar encontrar participante com mesmo nome na sala (pode ser o mesmo usuário com sessão diferente)
                $currentParticipant = $room->participants()
                    ->where('name', $savedName)
                    ->where('last_activity', '>=', now()->subMinutes(5)) // Apenas se foi ativo recentemente
                    ->orderBy('last_activity', 'desc')
                    ->first();
                
                // Se encontrou, atualizar session_id para a sessão atual
                if ($currentParticipant) {
                    $currentParticipant->update([
                        'session_id' => $sessionId,
                        'last_activity' => now(),
                    ]);
                }
            }
        }
        
        // Se o usuário é o criador e não tem participante, criar automaticamente
        if ($isCreator && !$currentParticipant) {
            // Tentar usar nome do cookie
            $creatorName = $request->cookie('participant_name');
            
            // Se não tiver nome no cookie, não criar participante ainda
            // O usuário precisará entrar na sala normalmente
            if ($creatorName) {
                // Criar participante para o criador automaticamente
                $currentParticipant = $room->participants()->create([
                    'name' => $creatorName,
                    'session_id' => $sessionId,
                    'is_observer' => false,
                    'last_activity' => now(),
                ]);
            }
        }
        
        // Se ainda não tem participante mas tem nome no cookie, criar um novo
        if (!$currentParticipant) {
            $savedName = $request->cookie('participant_name');
            if ($savedName) {
                $currentParticipant = $room->participants()->create([
                    'name' => $savedName,
                    'session_id' => $sessionId,
                    'is_observer' => false,
                    'last_activity' => now(),
                ]);
            }
        }
        
        // Atualizar last_activity do participante atual ANTES da limpeza
        // Isso garante que ele não seja removido
        if ($currentParticipant) {
            $currentParticipant->update(['last_activity' => now()]);
        }
        
        // Não remover participantes inativos - manter histórico
        // Apenas não contá-los como ativos nas estatísticas em tempo real
        // Os dados permanecem no banco para estatísticas históricas
        
        $activeStory = $room->activeStory();
        $hasUnrevealedStory = $room->hasActiveUnrevealedStory();
        
        // Carregar participantes com votos se houver história ativa
        if ($activeStory) {
            // Carregar votos da história também
            $activeStory->load('votes.participant');
            
            $participants = $room->participants()
                ->with(['votes' => function($query) use ($activeStory) {
                    $query->where('story_id', $activeStory->id);
                }])
                ->get();
        } else {
            $participants = $room->participants()->get();
        }
        
        // Usar o participante atual que já foi buscado/atualizado acima
        $participant = $currentParticipant;

        // Ler nome do cookie para preencher automaticamente
        $savedName = $request->cookie('participant_name');

        return view('rooms.show', compact('room', 'activeStory', 'participants', 'participant', 'hasUnrevealedStory', 'isCreator', 'savedName'));
    }

    public function getVotesStatus(Request $request, $code)
    {
        // Cache key única por sala e história
        $room = Room::where('code', $code)->firstOrFail();
        $activeStory = $room->activeStory();
        $sessionId = Session::getId();
        $isCreator = $room->creator_session_id === $sessionId;
        
        // Cache de 2 segundos para reduzir carga no banco
        // Apenas cache se houver história ativa
        $cacheKey = "votes_status_{$room->id}_" . ($activeStory ? $activeStory->id : 'no_story');
        $cacheTime = 2; // 2 segundos de cache
        
        // Verificar cache apenas se não for o criador (criador sempre vê dados atualizados)
        if (!$isCreator && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            // Adicionar informações do participante atual ao cache
            $cached['is_creator'] = $isCreator;
            return response()->json($cached);
        }
        
        // Atualizar last_activity do participante atual ANTES da limpeza
        $currentParticipant = $room->participants()
            ->where('session_id', $sessionId)
            ->first();
        
        // Se não encontrou por session_id, tentar encontrar por cookie (fallback para sessão que mudou)
        if (!$currentParticipant) {
            $savedName = $request->cookie('participant_name');
            if ($savedName) {
                // Tentar encontrar participante com mesmo nome na sala (pode ser o mesmo usuário com sessão diferente)
                $currentParticipant = $room->participants()
                    ->where('name', $savedName)
                    ->where('last_activity', '>=', now()->subMinutes(5)) // Apenas se foi ativo recentemente
                    ->orderBy('last_activity', 'desc')
                    ->first();
                
                // Se encontrou, atualizar session_id para a sessão atual
                if ($currentParticipant) {
                    $currentParticipant->update([
                        'session_id' => $sessionId,
                        'last_activity' => now(),
                    ]);
                }
            }
        }
        
        // Se é criador e não tem participante, criar automaticamente
        if ($isCreator && !$currentParticipant) {
            $creatorName = $request->cookie('participant_name');
            // Só criar se tiver nome no cookie
            if ($creatorName) {
                $currentParticipant = $room->participants()->create([
                    'name' => $creatorName,
                    'session_id' => $sessionId,
                    'is_observer' => false,
                    'last_activity' => now(),
                ]);
            }
        }
        
        if ($currentParticipant) {
            $currentParticipant->update(['last_activity' => now()]);
        }
        
        // Não remover participantes inativos - manter histórico
        // Apenas não contá-los como ativos nas estatísticas em tempo real
        // Os dados permanecem no banco para estatísticas históricas
        
        // Contar histórias não reveladas
        $unrevealedCount = $room->stories()->where('is_revealed', false)->count();
        
        // Buscar novos arremessos de emoticons (últimos 3 segundos)
        // Filtrar para não retornar emojis arremessados pelo próprio participante
        // (ele já viu o emoji quando arremessou, não precisa ver novamente via polling)
        $currentParticipantId = $currentParticipant ? $currentParticipant->id : null;
        $recentEmojiThrows = \App\Models\EmojiThrow::where('room_id', $room->id)
            ->where('created_at', '>=', now()->subSeconds(3))
            ->where('from_participant_id', '!=', $currentParticipantId) // Excluir emojis arremessados pelo próprio usuário
            ->with(['fromParticipant', 'toParticipant'])
            ->get()
            ->map(function($throw) {
                return [
                    'emoji' => $throw->emoji,
                    'to_participant_id' => $throw->to_participant_id,
                    'from_name' => $throw->fromParticipant->name,
                ];
            });
        
        // Buscar todos os participantes para retornar na resposta
        $allParticipants = $room->participants()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                ];
            });
        
        if (!$activeStory) {
            $response = [
                'has_story' => false,
                'is_creator' => $isCreator,
                'unrevealed_count' => $unrevealedCount,
                'emoji_throws' => $recentEmojiThrows,
                'participants' => $allParticipants,
                'participants_count' => $allParticipants->count(),
            ];
            
            // Cachear também quando não há história
            if (!$isCreator) {
                Cache::put($cacheKey, $response, $cacheTime);
            }
            
            return response()->json($response);
        }

        // Recarregar votos
        $activeStory->load('votes.participant');

        // Atualizar last_activity do participante atual novamente (se ainda não foi atualizado)
        if ($currentParticipant) {
            $currentParticipant->update(['last_activity' => now()]);
        }
        
        // Buscar todos os participantes e seus votos (otimizado com eager loading)
        $participants = $room->participants()
            ->with(['votes' => function($query) use ($activeStory) {
                $query->where('story_id', $activeStory->id);
            }])
            ->where('last_activity', '>=', now()->subMinutes(10)) // Apenas participantes ativos recentemente
            ->orderBy('created_at', 'asc')
            ->get();

        // NÃO agrupar por nome - mostrar TODOS os participantes
        // Isso garante que votos de diferentes dispositivos sejam exibidos corretamente
        $currentParticipantId = $currentParticipant ? $currentParticipant->id : null;
        $participantsToShow = [];
        $participantsWithVotes = [];
        
        // Primeiro, adicionar todos os participantes que têm voto
        foreach ($participants as $p) {
            $vote = $p->votes->first();
            if ($vote !== null) {
                $participantsWithVotes[$p->id] = $p;
                $participantsToShow[$p->id] = $p;
            }
        }
        
        // Depois, adicionar o participante atual se ainda não estiver na lista
        if ($currentParticipantId && !isset($participantsToShow[$currentParticipantId])) {
            $currentParticipant->load(['votes' => function($query) use ($activeStory) {
                $query->where('story_id', $activeStory->id);
            }]);
            $participantsToShow[$currentParticipantId] = $currentParticipant;
        }
        
        // Por fim, adicionar outros participantes que não têm voto mas estão ativos
        // (para mostrar quem ainda não votou)
        foreach ($participants as $p) {
            if (!isset($participantsToShow[$p->id])) {
                $vote = $p->votes->first();
                // Adicionar apenas se não tiver voto (para mostrar quem ainda não votou)
                if ($vote === null && $p->last_activity >= now()->subMinutes(5)) {
                    $participantsToShow[$p->id] = $p;
                }
            }
        }

        $votesStatus = [];
        foreach ($participantsToShow as $p) {
            $vote = $p->votes->first();
            $votesStatus[] = [
                'id' => $p->id,
                'name' => $p->name,
                'has_voted' => $vote !== null,
                'vote_value' => $activeStory->is_revealed && $vote ? $vote->value : null,
            ];
        }

        // Verificar se todos votaram o mesmo número (apenas se revelado)
        $allSame = false;
        if ($activeStory->is_revealed) {
            $allVotes = $activeStory->votes()->get();
            $numericVotes = $allVotes
                ->filter(function($vote) {
                    return is_numeric($vote->value) && !in_array($vote->value, ['?', 'coffee']);
                })
                ->map(function($vote) {
                    return (string) $vote->value; // Converter para string para comparação exata
                })
                ->values();
            
            // Verificar se todos votaram igual (precisa ter pelo menos 2 votos numéricos)
            if ($numericVotes->count() >= 2) {
                $firstVote = $numericVotes->first();
                $allSame = $numericVotes->every(function($vote) use ($firstVote) {
                    return $vote === $firstVote; // Comparação exata de strings
                });
            }
        }
        
        $response = [
            'has_story' => true,
            'is_creator' => $isCreator,
            'unrevealed_count' => $unrevealedCount,
            'story' => [
                'id' => $activeStory->id,
                'title' => $activeStory->title,
                'is_revealed' => $activeStory->is_revealed,
                'average_vote' => $activeStory->average_vote,
                'votes_count' => $activeStory->votes->count(),
            ],
            'votes' => $votesStatus,
            'total_votes' => count(array_filter($votesStatus, fn($v) => $v['has_voted'])),
            'total_participants' => count($votesStatus),
            'participants' => $allParticipants,
            'participants_count' => $allParticipants->count(),
            'emoji_throws' => $recentEmojiThrows,
            'all_same_vote' => $allSame,
        ];
        
        // Cachear resposta por 2 segundos (apenas para não-criadores)
        if (!$isCreator) {
            Cache::put($cacheKey, $response, $cacheTime);
        }
        
        return response()->json($response);
    }

    public function join(Request $request, $code)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room = Room::where('code', $code)->firstOrFail();
        $sessionId = Session::getId();

        // Verificar se já existe participante com esta session_id (idempotência)
        $participant = $room->participants()
            ->where('session_id', $sessionId)
            ->first();

        if ($participant) {
            // Se já existe, apenas atualizar nome e status se necessário
            $participant->update([
                'name' => $request->name,
                'last_activity' => now(),
            ]);
        } else {
            // Criar novo participante apenas se não existir
                $participant = $room->participants()->create([
                    'name' => $request->name,
                    'session_id' => $sessionId,
                    'is_observer' => false,
                    'last_activity' => now(),
                ]);
        }

        // Salvar nome em cookie (válido por 30 dias)
        return redirect()
            ->route('rooms.show', $room->code)
            ->cookie('participant_name', $request->name, 60 * 24 * 30); // 30 dias
    }

    public function leave(Request $request, $code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $sessionId = Session::getId();

        // Encontrar participante
        $participant = $room->participants()
            ->where('session_id', $sessionId)
            ->first();

        if (!$participant) {
            return redirect()->route('welcome')->with('error', 'Você não está nesta sala.');
        }

        // Verificar se é o criador
        $isCreator = $room->creator_session_id === $sessionId;
        
        if ($isCreator) {
            // Se for criador, verificar se há outros participantes
            $otherParticipants = $room->participants()
                ->where('id', '!=', $participant->id)
                ->count();
            
            if ($otherParticipants > 0) {
                // Transferir criação para o primeiro participante disponível
                $newCreator = $room->participants()
                    ->where('id', '!=', $participant->id)
                    ->first();
                
                if ($newCreator) {
                    $room->update(['creator_session_id' => $newCreator->session_id]);
                }
            } else {
                // Se não há outros participantes, desativar a sala
                $room->update(['is_active' => false]);
            }
        }

        // Remover participante (os votos são mantidos por cascade ou podem ser removidos)
        $participant->delete();

        return redirect()->route('welcome')->with('success', 'Você saiu da sala com sucesso.');
    }

    public function heartbeat(Request $request, $code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $sessionId = Session::getId();
        $isCreator = $room->creator_session_id === $sessionId;

        // Buscar participante por session_id
        $participant = $room->participants()
            ->where('session_id', $sessionId)
            ->first();

        // Se não encontrou e é criador, criar automaticamente
        if (!$participant && $isCreator) {
            $creatorName = $request->cookie('participant_name');
            // Só criar se tiver nome no cookie
            if ($creatorName) {
                $participant = $room->participants()->create([
                    'name' => $creatorName,
                    'session_id' => $sessionId,
                    'is_observer' => false,
                    'last_activity' => now(),
                ]);
            }
        }
        // Se não encontrou mas tem nome no cookie, tentar encontrar ou criar
        elseif (!$participant) {
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
                } else {
                    // Criar novo participante
                    $participant = $room->participants()->create([
                        'name' => $savedName,
                        'session_id' => $sessionId,
                        'is_observer' => false,
                        'last_activity' => now(),
                    ]);
                }
            }
        } else {
            // Atualizar last_activity
            $participant->update(['last_activity' => now()]);
        }

        return response()->json(['success' => true]);
    }
}

