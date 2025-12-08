@extends('layouts.app')

@section('title', 'Criar Sala - Planning Poker')

@section('content')
<div class="card">
    <h1>Criar Nova Sala</h1>
    
    <form method="POST" action="{{ route('rooms.store') }}" id="create-room-form">
        @csrf
        
        <div class="form-group">
            <label for="room_name">Nome da Sala</label>
            <input 
                type="text" 
                id="room_name" 
                name="name" 
                required 
                placeholder="Ex: Sprint 2024 - Backend"
                value="{{ old('name') }}"
            >
        </div>
        
        <div class="form-group">
            <label for="creator_name">Seu Nome</label>
            <input 
                type="text" 
                id="creator_name" 
                name="creator_name" 
                required 
                placeholder="Digite seu nome"
                value="{{ old('creator_name', request()->cookie('participant_name')) }}"
            >
        </div>
        
        <button type="submit" class="btn">Criar Sala</button>
        <a href="{{ route('welcome') }}" class="btn btn-secondary" style="margin-left: 10px;">Voltar</a>
    </form>
</div>
@endsection

