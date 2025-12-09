<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Planning Poker')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Meta Tags para Compartilhamento Social (WhatsApp, Facebook, etc.) -->
    <meta name="description" content="@yield('description', 'Sistema gratuito e colaborativo de Planning Poker para estimativas ágeis. Crie salas, vote em tempo real e alcance consenso rapidamente!')">
    
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'Planning Poker - Sistema de Estimativas Ágeis')">
    <meta property="og:description" content="@yield('og_description', 'Sistema gratuito e colaborativo de Planning Poker. Crie salas, vote em tempo real, revele resultados e alcance consenso rapidamente com sua equipe!')">
    <meta property="og:image" content="{{ url('/og-image.svg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="Planning Poker">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Planning Poker - Sistema de Estimativas Ágeis')">
    <meta name="twitter:description" content="@yield('og_description', 'Sistema gratuito e colaborativo de Planning Poker. Crie salas, vote em tempo real e alcance consenso rapidamente!')">
    <meta name="twitter:image" content="{{ url('/og-image.svg') }}">
    
    <!-- Favicon - Compatível com todos os navegadores -->
    <link rel="icon" type="image/svg+xml" href="{{ url('/favicon.svg') }}">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="{{ url('/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ url('/favicon.svg') }}">
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/favicon.svg') }}">
    <meta name="theme-color" content="#667eea">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 12px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 12px;
        }
        
        h1 {
            font-size: 20px;
            color: #333;
            margin-bottom: 8px;
        }
        
        h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 12px;
        }
        
        h3 {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .btn:hover {
            background: #5568d3;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        button[type="submit"]:hover {
            background: #dc3545 !important;
            color: white !important;
        }
        
        .form-group {
            margin-bottom: 12px;
        }
        
        label {
            display: block;
            margin-bottom: 4px;
            color: #555;
            font-weight: 500;
            font-size: 13px;
        }
        
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .alert {
            padding: 10px 16px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 13px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .vote-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
            gap: 8px;
            margin: 12px 0;
        }
        
        .vote-card {
            background: white;
            border: 2px solid #667eea;
            border-radius: 6px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 18px;
            font-weight: 600;
            color: #667eea;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .vote-card:hover {
            background: #667eea;
            color: white;
            transform: scale(1.05);
        }
        
        .vote-card.selected {
            background: #667eea;
            color: white;
        }
        
        .participants-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 8px;
            margin-top: 12px;
            position: relative;
        }
        
        .participant-card {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            border-left: 3px solid #667eea;
            position: relative;
            transition: all 0.2s;
            overflow: visible;
            z-index: 1;
        }
        
        .participant-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            z-index: 100;
        }
        
        .emoji-picker {
            position: fixed;
            z-index: 1000;
            background: white;
            border-radius: 8px;
            padding: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            animation: fadeIn 0.2s ease;
            pointer-events: auto;
            width: 200px;
            box-sizing: border-box;
            /* Posicionamento será calculado dinamicamente via JavaScript */
        }
        
        .emoji-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            width: 100%;
            box-sizing: border-box;
        }
        
        /* Responsividade para emoji-picker em telas pequenas */
        @media (max-width: 480px) {
            .emoji-picker {
                max-width: calc(100vw - 40px);
                padding: 6px;
                min-width: auto;
            }
            
            .emoji-options {
                grid-template-columns: repeat(4, 1fr);
                gap: 4px;
            }
            
            .emoji-btn {
                font-size: 18px;
                padding: 4px;
            }
        }
        
        /* Garantir que o picker não saia da viewport */
        .emoji-picker {
            max-height: 90vh;
            overflow: visible;
        }
        
        /* Melhorar responsividade em telas muito pequenas */
        @media (max-width: 360px) {
            .emoji-picker {
                width: 180px;
                padding: 6px;
            }
            
            .emoji-options {
                gap: 4px;
            }
            
            .emoji-btn {
                padding: 6px;
                font-size: 20px;
            }
        }
        
        .emoji-btn {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            font-size: 22px;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.2s;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            aspect-ratio: 1;
            min-height: 40px;
        }
        
        .emoji-btn:hover {
            background: #e9ecef;
            border-color: #667eea;
            transform: scale(1.1);
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.2);
        }
        
        .emoji-btn:active {
            transform: scale(0.95);
            background: #dee2e6;
        }
        
        .flying-emoji {
            position: fixed;
            font-size: 32px;
            pointer-events: none;
            z-index: 9999;
            will-change: transform, opacity;
        }
        
        @keyframes flyToTarget {
            0% {
                opacity: 1;
                transform: translate(0, 0) scale(1) rotate(0deg);
            }
            50% {
                transform: translate(calc(var(--end-x) - var(--start-x)) * 0.5, calc(var(--end-y) - var(--start-y)) * 0.5)) scale(1.3) rotate(180deg);
            }
            100% {
                opacity: 0.8;
                transform: translate(calc(var(--end-x) - var(--start-x)), calc(var(--end-y) - var(--start-y))) scale(1.5) rotate(360deg);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
        
        .emoji-hit {
            animation: hitEffect 0.4s ease;
        }
        
        @keyframes hitEffect {
            0%, 100% {
                transform: scale(1);
            }
            25% {
                transform: scale(1.15) rotate(-5deg);
            }
            50% {
                transform: scale(1.1) rotate(5deg);
            }
            75% {
                transform: scale(1.05) rotate(-2deg);
            }
        }
        
        .result-card.celebrate {
            animation: resultBlink 0.3s ease-in-out 10 !important;
        }
        
        @keyframes resultBlink {
            0% {
                background-color: white !important;
                border-color: #667eea !important;
                transform: scale(1) !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            }
            50% {
                background-color: #28a745 !important;
                border-color: #28a745 !important;
                border-width: 4px !important;
                transform: scale(1.2) !important;
                box-shadow: 0 0 40px rgba(40, 167, 69, 1) !important;
            }
            100% {
                background-color: white !important;
                border-color: #667eea !important;
                transform: scale(1) !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
            }
        }
        
        .result-card.celebrate .result-value {
            animation: resultValueBlink 0.3s ease-in-out 10 !important;
        }
        
        @keyframes resultValueBlink {
            0% {
                color: #667eea !important;
                transform: scale(1) !important;
            }
            50% {
                color: white !important;
                transform: scale(1.3) !important;
                font-weight: bold !important;
            }
            100% {
                color: #667eea !important;
                transform: scale(1) !important;
            }
        }
        
        .result-card.celebrate .result-label {
            animation: resultLabelBlink 0.3s ease-in-out 10 !important;
        }
        
        @keyframes resultLabelBlink {
            0% {
                color: #666 !important;
            }
            50% {
                color: white !important;
                font-weight: bold !important;
            }
            100% {
                color: #666 !important;
            }
        }
        
        /* Mensagem de consenso */
        .consensus-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10002;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px 40px;
            border-radius: 50px;
            box-shadow: 0 4px 30px rgba(40, 167, 69, 0.8);
            font-size: 24px;
            font-weight: bold;
            pointer-events: none;
            animation: consensusMessageSlide 0.5s ease-out forwards;
            display: flex;
            align-items: center;
            gap: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        /* Overlay verde para destacar ainda mais */
        .consensus-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(40, 167, 69, 0.2);
            z-index: 10001;
            pointer-events: none;
            animation: consensusOverlay 0.3s ease-out forwards;
        }
        
        @keyframes consensusOverlay {
            0% {
                opacity: 0;
            }
            50% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }
        
        @keyframes consensusMessageSlide {
            0% {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
        
        .consensus-message.fade-out {
            animation: consensusMessageFadeOut 0.5s ease-in forwards;
        }
        
        @keyframes consensusMessageFadeOut {
            from {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
            to {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
        }
        
        .participant-name {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 4px;
            color: #333;
        }
        
        .participant-vote {
            color: #666;
            font-size: 12px;
        }
        
        .rooms-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .room-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #667eea;
            transition: all 0.2s;
        }
        
        .room-item:hover {
            background: #f0f0f0;
            transform: translateX(2px);
        }
        
        /* Responsividade para lista de salas */
        @media (max-width: 480px) {
            .room-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .room-item .btn {
                width: 100%;
            }
        }
        
        .story-card {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 12px;
            border-left: 3px solid #667eea;
        }
        
        .story-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
            color: #333;
        }
        
        .story-description {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
        }
        
        .results {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 12px;
            margin-top: 12px;
        }
        
        .result-card {
            background: white;
            border: 2px solid #667eea;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }
        
        .result-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .result-label {
            color: #666;
            font-size: 11px;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .room-header {
            padding: 12px 16px;
        }
        
        .room-code-link {
            background: none;
            border: 1px solid #667eea;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 13px;
            font-weight: 600;
            color: #667eea;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
            letter-spacing: 1px;
        }
        
        .room-code-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-1px);
        }
        
        .room-code-link:active {
            transform: translateY(0);
        }
        
        .vote-status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        
        .vote-status-indicator.voted {
            background: #28a745;
        }
        
        .vote-status-indicator.pending {
            background: #dc3545;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        /* Responsividade - Mobile (iPhone) */
        @media (max-width: 480px) {
            body {
                padding: 8px;
            }
            
            .card {
                padding: 12px;
                margin-bottom: 8px;
            }
            
            .room-header {
                padding: 10px 12px;
            }
            
            .room-header > div {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            h1 {
                font-size: 16px;
            }
            
            h2 {
                font-size: 14px;
            }
            
            .room-code-link {
                font-size: 12px;
                padding: 3px 8px;
            }
            
            .vote-cards {
                grid-template-columns: repeat(4, 1fr);
                gap: 6px;
            }
            
            .vote-card {
                padding: 10px 6px;
                font-size: 16px;
                min-height: 45px;
            }
            
            .participants-list {
                grid-template-columns: repeat(2, 1fr);
                gap: 6px;
            }
            
            .participant-card {
                padding: 8px;
            }
            
            .code-display {
                font-size: 14px;
                padding: 8px;
                letter-spacing: 1px;
            }
            
            .results {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            
            .result-value {
                font-size: 20px;
            }
        }
        
        /* Responsividade - Tablet (iPad) */
        @media (min-width: 481px) and (max-width: 768px) {
            .vote-cards {
                grid-template-columns: repeat(6, 1fr);
                gap: 8px;
            }
            
            .participants-list {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .results {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Responsividade - Desktop */
        @media (min-width: 769px) {
            .vote-cards {
                grid-template-columns: repeat(6, 1fr);
                max-width: 600px;
                margin: 12px auto;
            }
            
            .participants-list {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
        }
        
        /* Melhorias de acessibilidade */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <!-- SweetAlert2 - Biblioteca de alertas moderna -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @yield('scripts')
</body>
</html>
