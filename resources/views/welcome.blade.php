@extends('layouts.app')

<meta property="og:title" content="Planning Poker">
<meta property="og:description" content="Sistema colaborativo de Planning Poker para estimativas ágeis. Crie salas, vote em tempo real e alcance consenso rapidamente com sua equipe!">
<meta property="og:image" content="/img/logo.jpg">
<meta property="og:url" content="https://planningpoker.soavelveiculos.com.br/">
<meta property="og:type" content="website">

@section('content')
<!-- Hero Section -->
<div class="card hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 24px; text-align: center; border: none; box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="font-size: 64px; margin-bottom: 16px; line-height: 1;">🎴</div>
        <h1 style="font-size: 36px; font-weight: 700; margin-bottom: 16px; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            Planning Poker
        </h1>
        <p style="font-size: 20px; margin-bottom: 8px; color: rgba(255,255,255,0.95); font-weight: 500;">
            Sistema de Estimativas Ágeis
        </p>
        <p style="font-size: 16px; margin-bottom: 24px; color: rgba(255,255,255,0.85); line-height: 1.6;">
            Crie salas colaborativas, vote em tempo real e alcance consenso rapidamente com sua equipe. 
            <strong>Gratuito, rápido e fácil de usar!</strong>
        </p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
            <a href="{{ route('dashboard') }}" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); font-size: 16px; padding: 12px 24px;">
                📊 Dashboard
            </a>
            <a href="{{ route('rooms.create') }}" class="btn" style="background: white; color: #667eea; font-size: 18px; padding: 14px 32px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                🚀 Criar Nova Sala
            </a>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="card" style="margin-top: 20px;">
    <h2 style="font-size: 24px; margin-bottom: 20px; color: #333; text-align: center;">✨ Por que usar nosso Planning Poker?</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 24px;">
        <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;">
            <div style="font-size: 48px; margin-bottom: 12px;">🆓</div>
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #333;">100% Gratuito</h3>
            <p style="font-size: 14px; color: #666; line-height: 1.5;">Sem custos, sem cadastro, sem complicação. Use quantas vezes quiser!</p>
        </div>
        <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;">
            <div style="font-size: 48px; margin-bottom: 12px;">⚡</div>
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #333;">Tempo Real</h3>
            <p style="font-size: 14px; color: #666; line-height: 1.5;">Votos e atualizações aparecem instantaneamente para todos os participantes.</p>
        </div>
        <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;">
            <div style="font-size: 48px; margin-bottom: 12px;">👥</div>
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #333;">Colaborativo</h3>
            <p style="font-size: 14px; color: #666; line-height: 1.5;">Múltiplos participantes podem votar simultaneamente na mesma sala.</p>
        </div>
        <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;">
            <div style="font-size: 48px; margin-bottom: 12px;">📱</div>
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #333;">Responsivo</h3>
            <p style="font-size: 14px; color: #666; line-height: 1.5;">Funciona perfeitamente em desktop, tablet e celular. Acesse de qualquer lugar!</p>
        </div>
    </div>
</div>

<!-- Main Action Card -->
<div class="card" style="background: white; border: 2px solid #667eea;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="flex: 1; min-width: 200px;">
            <h2 style="font-size: 20px; margin-bottom: 8px; color: #333;">🎯 Comece Agora</h2>
            <p style="color: #666; font-size: 14px; margin: 0;">
                Crie sua primeira sala de Planning Poker em segundos
            </p>
        </div>
        <a href="{{ route('rooms.create') }}" class="btn" style="font-size: 16px; padding: 12px 24px;">
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

<!-- How to Use Section -->
<div class="card" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <h2 style="font-size: 24px; margin-bottom: 20px; color: #333; text-align: center;">📖 Como Funciona?</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 32px; margin-bottom: 8px; text-align: center;">1️⃣</div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 8px; color: #333; text-align: center;">Crie uma Sala</h3>
            <p style="font-size: 13px; color: #666; line-height: 1.5; text-align: center;">Crie uma nova sala ou entre em uma sala existente usando o código</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 32px; margin-bottom: 8px; text-align: center;">2️⃣</div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 8px; color: #333; text-align: center;">Vote</h3>
            <p style="font-size: 13px; color: #666; line-height: 1.5; text-align: center;">Os participantes votam usando os cartões de Planning Poker (Fibonacci)</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 32px; margin-bottom: 8px; text-align: center;">3️⃣</div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 8px; color: #333; text-align: center;">Revele</h3>
            <p style="font-size: 13px; color: #666; line-height: 1.5; text-align: center;">Revele os votos para ver os resultados e a média automaticamente</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 32px; margin-bottom: 8px; text-align: center;">4️⃣</div>
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 8px; color: #333; text-align: center;">Consenso</h3>
            <p style="font-size: 13px; color: #666; line-height: 1.5; text-align: center;">Discuta as diferenças e chegue a um consenso com sua equipe</p>
        </div>
    </div>
</div>

<div class="card" style="text-align: center; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border: none;">
    <p style="color: #666; font-size: 13px; margin-bottom: 12px;">
        Desenvolvido com ❤️ por
    </p>
    <p style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 16px;">
        João Filipi Britto
    </p>
    <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
        <a 
            href="https://github.com/jfbritto" 
            target="_blank" 
            rel="noopener noreferrer"
            style="display: inline-flex; align-items: center; gap: 6px; color: #333; text-decoration: none; padding: 8px 16px; background: white; border-radius: 6px; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle;">
                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
            <span style="font-size: 14px; font-weight: 500;">GitHub</span>
        </a>
        <a 
            href="https://www.linkedin.com/in/jfbritto/" 
            target="_blank" 
            rel="noopener noreferrer"
            style="display: inline-flex; align-items: center; gap: 6px; color: #0077b5; text-decoration: none; padding: 8px 16px; background: white; border-radius: 6px; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="vertical-align: middle;">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
            </svg>
            <span style="font-size: 14px; font-weight: 500;">LinkedIn</span>
        </a>
    </div>
</div>
@endsection

