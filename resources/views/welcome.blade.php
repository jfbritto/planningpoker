@extends('layouts.app')

@section('title', 'Planning Poker - Início')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <h1 style="margin-bottom: 4px;">🎴 Planning Poker</h1>
            <p style="color: #666; font-size: 13px; margin: 0;">
                Sistema simples e objetivo para estimativas ágeis
            </p>
        </div>
        <a href="{{ route('rooms.create') }}" class="btn">
            Criar Nova Sala
        </a>
    </div>
</div>

@if($rooms && $rooms->count() > 0)
    <div class="card">
        <h2 style="margin-bottom: 16px;">Salas Ativas ({{ $rooms->count() }})</h2>
        <div class="rooms-list">
            @foreach($rooms as $room)
                <div class="room-item">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <h3 style="margin: 0; font-size: 16px;">{{ $room->name }}</h3>
                            <span style="font-size: 11px; color: #999; background: #f3f4f6; padding: 2px 6px; border-radius: 10px; font-family: monospace;">
                                {{ $room->code }}
                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #666;">
                            <span>👥 {{ $room->participants_count }} participante(s)</span>
                            <span>•</span>
                            <span>🕐 {{ $room->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <a href="{{ route('rooms.show', $room->code) }}" class="btn" style="padding: 6px 16px; font-size: 13px;">
                        Entrar
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="card" style="text-align: center; padding: 40px;">
        <p style="color: #666; font-size: 14px; margin-bottom: 16px;">Nenhuma sala ativa no momento</p>
        <a href="{{ route('rooms.create') }}" class="btn">
            Criar Primeira Sala
        </a>
    </div>
@endif

<div class="card">
    <h2 style="font-size: 16px; margin-bottom: 12px;">Como usar:</h2>
    <ol style="line-height: 1.8; color: #666; font-size: 13px; padding-left: 20px;">
        <li>Crie uma nova sala ou entre em uma sala existente</li>
        <li>Os participantes votam usando os cartões de Planning Poker</li>
        <li>Revele os votos para ver os resultados</li>
        <li>Discuta as diferenças e chegue a um consenso</li>
    </ol>
</div>
@endsection

