@extends('layouts.app')

@section('title', 'Dashboard - Planning Poker')
@section('description', 'Estatísticas e informações do sistema Planning Poker')
@section('og_title', 'Dashboard - Planning Poker')
@section('og_description', 'Visualize estatísticas, salas recentes e atividades do sistema Planning Poker')

@section('content')
<!-- Header do Dashboard -->
<div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 24px; border: none; box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 8px; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                📊 Dashboard
            </h1>
            <p style="font-size: 16px; color: rgba(255,255,255,0.9); margin: 0;">
                Estatísticas e informações do sistema
            </p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('welcome') }}" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                🏠 Início
            </a>
            <a href="{{ route('rooms.create') }}" class="btn" style="background: white; color: #667eea;">
                ➕ Nova Sala
            </a>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas Gerais -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-top: 20px;">
    <!-- Total de Salas -->
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #5568d3 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <p style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total de Salas</p>
                <h2 style="font-size: 36px; font-weight: 700; margin: 0;">{{ $totalRooms }}</h2>
                <p style="font-size: 12px; opacity: 0.8; margin-top: 8px;">
                    {{ $activeRooms }} ativas
                </p>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">🏠</div>
        </div>
    </div>

    <!-- Total de Participantes -->
    <div class="card" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <p style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total de Participantes</p>
                <h2 style="font-size: 36px; font-weight: 700; margin: 0;">{{ $totalParticipants }}</h2>
                <p style="font-size: 12px; opacity: 0.8; margin-top: 8px;">
                    {{ $activeParticipants }} ativos agora
                </p>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">👥</div>
        </div>
    </div>

    <!-- Total de Histórias -->
    <div class="card" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <p style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total de Histórias</p>
                <h2 style="font-size: 36px; font-weight: 700; margin: 0;">{{ $totalStories }}</h2>
                <p style="font-size: 12px; opacity: 0.8; margin-top: 8px;">
                    {{ $revealedStories }} reveladas
                </p>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">📋</div>
        </div>
    </div>

    <!-- Total de Votos -->
    <div class="card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <p style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">Total de Votos</p>
                <h2 style="font-size: 36px; font-weight: 700; margin: 0;">{{ $totalVotes }}</h2>
                <p style="font-size: 12px; opacity: 0.8; margin-top: 8px;">
                    Média: {{ $avgVotesPerStory }}/história
                </p>
            </div>
            <div style="font-size: 48px; opacity: 0.3;">🗳️</div>
        </div>
    </div>
</div>

<!-- Estatísticas de Hoje -->
<div class="card" style="margin-top: 20px;">
    <h2 style="font-size: 20px; margin-bottom: 20px; color: #333;">📈 Atividades de Hoje (24h)</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="font-size: 32px; margin-bottom: 8px;">🏠</div>
            <h3 style="font-size: 24px; font-weight: 600; color: #667eea; margin: 0;">{{ $roomsToday }}</h3>
            <p style="font-size: 14px; color: #666; margin-top: 4px;">Salas criadas</p>
        </div>
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="font-size: 32px; margin-bottom: 8px;">👥</div>
            <h3 style="font-size: 24px; font-weight: 600; color: #28a745; margin: 0;">{{ $participantsToday }}</h3>
            <p style="font-size: 14px; color: #666; margin-top: 4px;">Participantes novos</p>
        </div>
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <div style="font-size: 32px; margin-bottom: 8px;">🗳️</div>
            <h3 style="font-size: 24px; font-weight: 600; color: #dc3545; margin: 0;">{{ $votesToday }}</h3>
            <p style="font-size: 14px; color: #666; margin-top: 4px;">Votos registrados</p>
        </div>
    </div>
</div>

<!-- Salas Recentes -->
<div class="card" style="margin-top: 20px;">
    <h2 style="font-size: 20px; margin-bottom: 20px; color: #333;">🕐 Salas Recentes</h2>
    @if($recentRooms->count() > 0)
        <div class="rooms-list">
            @foreach($recentRooms as $room)
                <div class="room-item">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <h3 style="margin: 0; font-size: 16px;">{{ $room->name }}</h3>
                            <span style="font-size: 11px; color: #999; background: #f3f4f6; padding: 2px 6px; border-radius: 10px; font-family: monospace;">
                                {{ $room->code }}
                            </span>
                            @if($room->is_active)
                                <span style="font-size: 11px; color: #28a745; background: #d4edda; padding: 2px 6px; border-radius: 10px;">
                                    ✓ Ativa
                                </span>
                            @else
                                <span style="font-size: 11px; color: #6c757d; background: #e2e3e5; padding: 2px 6px; border-radius: 10px;">
                                    Inativa
                                </span>
                            @endif
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #666;">
                            <span>👥 {{ $room->participants_count }} participante(s)</span>
                            <span>•</span>
                            <span>📋 {{ $room->stories_count }} história(s)</span>
                            <span>•</span>
                            <span>🕐 {{ $room->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if($room->is_active)
                        <a href="{{ route('rooms.show', $room->code) }}" class="btn" style="padding: 6px 16px; font-size: 13px;">
                            Entrar
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p style="color: #666; text-align: center; padding: 40px;">
            Nenhuma sala encontrada
        </p>
    @endif
</div>

<!-- Informações Adicionais -->
<div class="card" style="margin-top: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <h2 style="font-size: 20px; margin-bottom: 16px; color: #333;">ℹ️ Informações do Sistema</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
        <div style="padding: 16px; background: white; border-radius: 8px;">
            <h3 style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">📊 Médias</h3>
            <p style="font-size: 13px; color: #666; margin: 4px 0;">
                <strong>{{ $avgParticipantsPerRoom }}</strong> participantes por sala
            </p>
            <p style="font-size: 13px; color: #666; margin: 4px 0;">
                <strong>{{ $avgVotesPerStory }}</strong> votos por história
            </p>
        </div>
        <div style="padding: 16px; background: white; border-radius: 8px;">
            <h3 style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px;">⚡ Status</h3>
            <p style="font-size: 13px; color: #666; margin: 4px 0;">
                <strong>{{ $activeRooms }}</strong> salas ativas
            </p>
            <p style="font-size: 13px; color: #666; margin: 4px 0;">
                <strong>{{ $activeParticipants }}</strong> participantes online
            </p>
        </div>
    </div>
</div>
@endsection
