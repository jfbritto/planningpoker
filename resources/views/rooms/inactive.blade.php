@extends('layouts.app')

@section('title', 'Sala Inativa - Planning Poker')
@section('description', 'Esta sala não está mais ativa.')

@section('content')
<div style="max-width: 600px; margin: 40px auto; padding: 20px;">
    <div class="card" style="text-align: center; padding: 40px 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div style="font-size: 64px; margin-bottom: 20px;">🔒</div>
        
        <h1 style="font-size: 24px; margin-bottom: 16px; color: #333;">
            Sala Não Está Mais Ativa
        </h1>
        
        <div style="margin-bottom: 24px; color: #666; line-height: 1.6;">
            <p style="margin-bottom: 12px;">
                A sala <strong>"{{ $room->name }}"</strong> (Código: <strong>{{ $room->code }}</strong>) não está mais ativa.
            </p>
            <p style="font-size: 14px; color: #999;">
                Esta sala foi desativada automaticamente após ficar inativa por mais de 24 horas ou quando todos os participantes saíram.
            </p>
        </div>
        
        <div style="margin-top: 32px;">
            <a href="{{ route('welcome') }}" class="btn" style="display: inline-block; padding: 12px 32px; font-size: 16px; text-decoration: none; background: linear-gradient(135deg, #667eea 0%, #5568d3 100%); color: white; border-radius: 8px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);">
                ← Voltar para Tela Inicial
            </a>
        </div>
        
        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #dee2e6;">
            <p style="font-size: 13px; color: #999; margin: 0;">
                💡 Dica: Você pode criar uma nova sala ou entrar em uma sala ativa.
            </p>
        </div>
    </div>
</div>

<style>
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4) !important;
    }
    
    .btn:active {
        transform: translateY(0);
    }
</style>
@endsection
