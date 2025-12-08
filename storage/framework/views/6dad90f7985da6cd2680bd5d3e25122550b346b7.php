<?php
    // Verificar se as variáveis existem antes de usar
    $participantsCount = isset($participants) ? $participants->count() : 0;
    $activeStoryExists = isset($activeStory) && $activeStory !== null;
    
    $votedCount = 0;
    if ($activeStoryExists && isset($participants)) {
        $votedCount = $participants->filter(function($p) use ($activeStory) { 
            return $p->votes->where('story_id', $activeStory->id)->isNotEmpty(); 
        })->count();
    }
    
    $hasActiveVoting = $activeStoryExists && !$activeStory->is_revealed;
    $isVotingComplete = $activeStoryExists && $votedCount === $participantsCount && $participantsCount > 0;
    
    // Descrição personalizada baseada no estado da sala
    if ($hasActiveVoting) {
        $statusText = $isVotingComplete 
            ? "Todos os {$participantsCount} participantes já votaram!" 
            : "{$votedCount} de {$participantsCount} participantes votaram. Entre e participe!";
        $description = "Sala: {$room->name} (Código: {$room->code}). {$statusText}";
    } else {
        $description = "Sala: {$room->name} (Código: {$room->code}). {$participantsCount} participante(s). Entre e participe das estimativas!";
    }
    
    $ogTitle = "Sala: {$room->name} - Planning Poker";
    $ogDescription = $description;
?>

<?php $__env->startSection('title', 'Sala: ' . $room->name . ' - Planning Poker'); ?>
<?php $__env->startSection('description', $description); ?>
<?php $__env->startSection('og_title', $ogTitle); ?>
<?php $__env->startSection('og_description', $ogDescription); ?>

<?php $__env->startSection('content'); ?>
<div class="card room-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="flex: 1; min-width: 200px;">
            <h1 style="margin-bottom: 8px; font-size: 18px;"><?php echo e($room->name); ?></h1>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span style="font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 0.5px;">Código:</span>
                <button id="room-code-btn" class="room-code-link" data-code="<?php echo e($room->code); ?>" title="Clique para copiar URL completa">
                    <span id="room-code-text"><?php echo e($room->code); ?></span>
                    <span id="room-code-copied" style="display: none; color: #28a745; font-size: 11px; margin-left: 6px;">✓ URL copiada!</span>
                </button>
            </div>
        </div>
        <?php if($participant): ?>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: #f0f9ff; border-radius: 20px; border: 1px solid #bae6fd;">
                    <div style="width: 8px; height: 8px; background: #28a745; border-radius: 50%;"></div>
                    <span style="font-size: 12px; color: #333; font-weight: 500;"><?php echo e($participant->name); ?></span>
                </div>
                <form method="POST" action="<?php echo e(route('rooms.leave', $room->code)); ?>" id="leave-room-form" style="margin: 0;">
                    <?php echo csrf_field(); ?>
                    <button type="button" onclick="confirmLeaveRoom()" style="padding: 6px 12px; font-size: 11px; background: transparent; border: 1px solid #dc3545; color: #dc3545; border-radius: 6px; cursor: pointer; transition: all 0.2s; font-weight: 500;">
                        Sair da Sala
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(!$participant): ?>
    <div class="card" style="background: #fff3cd; border-left: 3px solid #ffc107;">
        <h3 style="font-size: 14px; margin-bottom: 12px;">Entrar na Sala</h3>
        <form method="POST" action="<?php echo e(route('rooms.join', $room->code)); ?>" id="join-form">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="name">Seu Nome</label>
                <input type="text" id="name" name="name" required placeholder="Digite seu nome" value="<?php echo e($savedName ?? ''); ?>">
            </div>
            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
<?php endif; ?>

<?php if($participant): ?>
    <div class="card" id="voting-area">
        <?php if($activeStory): ?>
            <div id="story-info" style="margin-bottom: 12px;">
                <h2 id="story-title" style="margin-bottom: 0;"><?php echo e($activeStory->title); ?></h2>
            </div>
            
            <?php if(!$activeStory->is_revealed): ?>
                <?php if($participant): ?>
                    <div id="vote-cards-container">
                        <h3 style="margin-bottom: 8px;">Selecione seu voto:</h3>
                        <div class="vote-cards">
                            <div class="vote-card" data-value="0">0</div>
                            <div class="vote-card" data-value="1">1</div>
                            <div class="vote-card" data-value="2">2</div>
                            <div class="vote-card" data-value="3">3</div>
                            <div class="vote-card" data-value="5">5</div>
                            <div class="vote-card" data-value="8">8</div>
                            <div class="vote-card" data-value="13">13</div>
                            <div class="vote-card" data-value="21">21</div>
                            <div class="vote-card" data-value="34">34</div>
                            <div class="vote-card" data-value="55">55</div>
                            <div class="vote-card" data-value="89">89</div>
                            <div class="vote-card" data-value="?">?</div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div id="votes-status" style="margin-top: 16px;">
                    <h3>Status dos Votos</h3>
                    <div class="participants-list" id="votes-list">
                        <?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(true): ?>
                                <?php
                                    $vote = $p->votes->first();
                                ?>
                                <div class="participant-card emoji-target" data-participant-id="<?php echo e($p->id); ?>" data-participant-name="<?php echo e($p->name); ?>">
                                    <div class="participant-name"><?php echo e($p->name); ?></div>
                                    <div class="participant-vote">
                                        <?php if($vote): ?>
                                            <span style="color: #28a745; font-weight: bold;">✓ Votou</span>
                                        <?php else: ?>
                                            <span style="color: #dc3545;">⏳ Aguardando...</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($participant && $participant->id !== $p->id): ?>
                                        <div class="emoji-picker" style="display: none;">
                                            <div class="emoji-options">
                                                <button class="emoji-btn" data-emoji="🎯" title="Arremessar">🎯</button>
                                                <button class="emoji-btn" data-emoji="🔥" title="Arremessar">🔥</button>
                                                <button class="emoji-btn" data-emoji="💥" title="Arremessar">💥</button>
                                                <button class="emoji-btn" data-emoji="⚡" title="Arremessar">⚡</button>
                                                <button class="emoji-btn" data-emoji="🎉" title="Arremessar">🎉</button>
                                                <button class="emoji-btn" data-emoji="🚀" title="Arremessar">🚀</button>
                                                <button class="emoji-btn" data-emoji="⏰" title="Arremessar">⏰</button>
                                                <button class="emoji-btn" data-emoji="💩" title="Arremessar">💩</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <p style="margin-top: 10px; color: #666; font-size: 12px;">
                        <span id="votes-count"><?php echo e($participants->filter(function($p) { return $p->votes->isNotEmpty(); })->count()); ?></span> de <span id="total-participants"><?php echo e($participants->count()); ?></span> votaram
                    </p>
                </div>
                
                <?php if($isCreator): ?>
                    <button id="reveal-btn" class="btn btn-success" style="margin-top: 12px; display: none;">Revelar Votos</button>
                <?php endif; ?>
            <?php else: ?>
                <div id="results-container">
                    <div class="results">
                        <div class="result-card">
                            <div class="result-value" id="average-vote"><?php echo e($activeStory->average_vote ?? 'N/A'); ?></div>
                            <div class="result-label">Média</div>
                        </div>
                        <div class="result-card">
                            <div class="result-value" id="total-votes-count"><?php echo e($activeStory->votes->count()); ?></div>
                            <div class="result-label">Votos</div>
                        </div>
                    </div>
                    
                    <h3 style="margin-top: 16px;">Votos dos Participantes</h3>
                    <div class="participants-list" id="revealed-votes-list">
                        <?php $__currentLoopData = $participants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(true): ?>
                                <?php
                                    $vote = $p->votes->first();
                                ?>
                                <div class="participant-card emoji-target" data-participant-id="<?php echo e($p->id); ?>" data-participant-name="<?php echo e($p->name); ?>">
                                    <div class="participant-name"><?php echo e($p->name); ?></div>
                                    <div class="participant-vote">
                                        Voto: <strong style="color: #667eea; font-size: 16px;"><?php echo e($vote ? $vote->value : 'Não votou'); ?></strong>
                                    </div>
                                    <?php if($participant && $participant->id !== $p->id): ?>
                                        <div class="emoji-picker" style="display: none;">
                                            <div class="emoji-options">
                                                <button class="emoji-btn" data-emoji="🎯" title="Arremessar">🎯</button>
                                                <button class="emoji-btn" data-emoji="🔥" title="Arremessar">🔥</button>
                                                <button class="emoji-btn" data-emoji="💥" title="Arremessar">💥</button>
                                                <button class="emoji-btn" data-emoji="⚡" title="Arremessar">⚡</button>
                                                <button class="emoji-btn" data-emoji="🎉" title="Arremessar">🎉</button>
                                                <button class="emoji-btn" data-emoji="🚀" title="Arremessar">🚀</button>
                                                <button class="emoji-btn" data-emoji="⏰" title="Arremessar">⏰</button>
                                                <button class="emoji-btn" data-emoji="💩" title="Arremessar">💩</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <?php if($isCreator): ?>
                        <button id="new-estimate-btn" class="btn btn-secondary" style="margin-top: 12px;">Nova Estimativa</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 24px;">
                <p style="color: #666; font-size: 14px; margin-bottom: 12px;">Nenhuma estimativa ativa</p>
                <?php if($isCreator): ?>
                    <button id="start-estimate-btn" class="btn">Iniciar Nova Estimativa</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>


<?php $__env->startSection('scripts'); ?>
    <script>
        // Função para confirmar saída da sala usando SweetAlert
        function confirmLeaveRoom() {
            Swal.fire({
                title: 'Sair da Sala?',
                text: 'Tem certeza que deseja sair da sala?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, sair',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('leave-room-form').submit();
                }
            });
        }
    </script>
<script>
    const roomCode = '<?php echo e($room->code); ?>';
    
    // Entrada automática se houver nome salvo no cookie
    <?php if(!$participant): ?>
    (function() {
        // Verificar se já tentou entrar automaticamente (evitar loops)
        if (sessionStorage.getItem('autoJoinAttempted')) {
            return;
        }
        
        // Tentar ler do cookie do servidor primeiro
        const savedName = <?php echo json_encode($savedName ?? null, 15, 512) ?>;
        
        // Se não tiver no servidor, tentar ler do cookie do navegador
        let participantName = savedName;
        if (!participantName) {
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'participant_name') {
                    participantName = decodeURIComponent(value);
                    break;
                }
            }
        }
        
        // Se houver nome salvo e o campo estiver vazio, preencher e entrar automaticamente
        if (participantName && participantName.trim()) {
            const nameInput = document.getElementById('name');
            const joinForm = document.getElementById('join-form');
            
            if (nameInput && joinForm && !nameInput.value.trim()) {
                nameInput.value = participantName.trim();
                
                // Marcar que tentou entrar automaticamente
                sessionStorage.setItem('autoJoinAttempted', 'true');
                
                // Entrar automaticamente após um pequeno delay para melhor UX
                setTimeout(() => {
                    joinForm.submit();
                }, 500);
            }
        }
    })();
    <?php endif; ?>
    
    // Copiar URL completa da sala
    const roomCodeBtn = document.getElementById('room-code-btn');
    if (roomCodeBtn) {
        roomCodeBtn.addEventListener('click', function() {
            const code = this.dataset.code;
            const codeText = document.getElementById('room-code-text');
            const copiedText = document.getElementById('room-code-copied');
            
            // Criar URL completa
            const fullUrl = window.location.origin + window.location.pathname;
            
            // Copiar URL completa para clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(fullUrl).then(() => {
                    codeText.style.display = 'none';
                    copiedText.style.display = 'inline';
                    setTimeout(() => {
                        codeText.style.display = 'inline';
                        copiedText.style.display = 'none';
                    }, 2000);
                });
            } else {
                // Fallback para navegadores antigos
                const textArea = document.createElement('textarea');
                textArea.value = fullUrl;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    codeText.style.display = 'none';
                    copiedText.style.display = 'inline';
                    setTimeout(() => {
                        codeText.style.display = 'inline';
                        copiedText.style.display = 'none';
                    }, 2000);
                } catch (err) {
                    console.error('Erro ao copiar:', err);
                }
                document.body.removeChild(textArea);
            }
        });
    }
    const activeStoryId = <?php echo e($activeStory ? $activeStory->id : 'null'); ?>;
    let isRevealed = <?php echo e($activeStory && $activeStory->is_revealed ? 'true' : 'false'); ?>;
    const isCreator = <?php echo e(isset($isCreator) && $isCreator ? 'true' : 'false'); ?>;
    const participantId = <?php echo e($participant ? $participant->id : 'null'); ?>;
    let lastUnrevealedCount = <?php echo e($room->stories()->where('is_revealed', false)->count()); ?>;
    let processedEmojiThrows = new Set(); // IDs de arremessos já processados
    
    // Sistema de votação assíncrono
    <?php if($participant): ?>
    document.querySelectorAll('.vote-card').forEach(card => {
        card.addEventListener('click', function() {
            if (!activeStoryId) return;
            
            // Remover seleção anterior
            document.querySelectorAll('.vote-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            const voteValue = this.dataset.value;
            
            // Enviar voto via AJAX
            fetch(`/rooms/${roomCode}/stories/${activeStoryId}/votes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ value: voteValue })
            })
            .then(response => {
                // Verificar se a resposta é OK antes de parsear JSON
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || `Erro HTTP: ${response.status}`);
                    }).catch(() => {
                        throw new Error(`Erro HTTP: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Atualizar status imediatamente
                    updateVotesStatus();
                } else {
                    console.error('Erro ao votar:', data.message || 'Erro desconhecido');
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao votar',
                        text: data.message || 'Erro ao registrar voto. Tente novamente.',
                        confirmButtonColor: '#667eea'
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao votar:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao votar',
                    text: error.message || 'Erro ao registrar voto. Tente novamente.',
                    confirmButtonColor: '#667eea'
                });
            });
        });
    });
    <?php endif; ?>
    
    // Função melhorada para celebrar consenso quando todos votam igual
    window.celebrateConsensus = function() {
        console.log('🎉 CELEBRAR CONSENSO - Função chamada!');
        
        // Remover elementos anteriores se existirem
        const existingMessage = document.querySelector('.consensus-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        const existingOverlay = document.querySelector('.consensus-overlay');
        if (existingOverlay) {
            existingOverlay.remove();
        }
        
        // Criar overlay verde que cobre toda a tela brevemente
        const overlay = document.createElement('div');
        overlay.className = 'consensus-overlay';
        document.body.appendChild(overlay);
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 600);
        
        // Criar mensagem de consenso grande e visível
        const message = document.createElement('div');
        message.className = 'consensus-message';
        message.innerHTML = '🎉 <span>CONSENSO ALCANÇADO!</span> 🎉';
        document.body.appendChild(message);
        console.log('Mensagem de consenso criada');
        
        // Remover mensagem após 4 segundos
        setTimeout(() => {
            message.classList.add('fade-out');
            setTimeout(() => {
                if (message.parentNode) {
                    message.parentNode.removeChild(message);
                }
            }, 500);
        }, 4000);
        
        // Função para aplicar efeito nos cards
        function applyCelebration() {
            const resultCards = document.querySelectorAll('.result-card');
            console.log('Buscando cards... Encontrados:', resultCards.length);
            
            if (resultCards.length === 0) {
                console.warn('Nenhum card encontrado, tentando novamente...');
                return false;
            }
            
            // Remover classe primeiro para garantir que a animação seja reiniciada
            resultCards.forEach(card => {
                card.classList.remove('celebrate');
            });
            
            // Forçar reflow
            void resultCards[0].offsetHeight;
            
            // Adicionar classe celebrate com estilo inline forçado
            resultCards.forEach((card, index) => {
                console.log(`Adicionando celebrate ao card ${index}`);
                // Remover qualquer animação anterior
                card.style.animation = 'none';
                card.style.backgroundColor = '';
                card.style.borderColor = '';
                card.style.transform = '';
                card.style.boxShadow = '';
                
                // Forçar reflow
                void card.offsetHeight;
                
                // Adicionar classe
                card.classList.add('celebrate');
                
                // Forçar estilo inline como backup
                setTimeout(() => {
                    if (card.classList.contains('celebrate')) {
                        card.style.animation = 'resultBlink 0.4s ease-in-out 8';
                        console.log(`Animação forçada no card ${index}`);
                    }
                }, 10);
            });
            
            console.log('Classe celebrate adicionada a', resultCards.length, 'cards');
            
            // Remover classe após animação (10 piscadas * 0.3s = 3s)
            setTimeout(() => {
                resultCards.forEach(card => {
                    card.classList.remove('celebrate');
                    card.style.animation = '';
                    card.style.backgroundColor = '';
                    card.style.borderColor = '';
                    card.style.transform = '';
                    card.style.boxShadow = '';
                });
                console.log('Classe celebrate removida');
            }, 3000);
            
            return true;
        }
        
        // Tentar aplicar imediatamente
        if (!applyCelebration()) {
            // Se não encontrou, tentar novamente após delays
            setTimeout(() => {
                if (!applyCelebration()) {
                    setTimeout(() => {
                        applyCelebration();
                    }, 500);
                }
            }, 200);
        }
    };
    
    // Alias para compatibilidade
    const createConfetti = window.celebrateConsensus;
    
    // TESTE: Função para testar o efeito manualmente (remover depois)
    window.testCelebration = function() {
        console.log('TESTE: Forçando celebração...');
        if (typeof celebrateConsensus === 'function') {
            celebrateConsensus();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Função não encontrada',
                text: 'Função celebrateConsensus não encontrada!',
                confirmButtonColor: '#667eea'
            });
        }
    };
    
    // Adicionar botão de teste temporário no console (apenas para debug)
    console.log('Para testar o efeito, execute: testCelebration()');
    
    // Botão de revelar votos
    const revealBtn = document.getElementById('reveal-btn');
    if (revealBtn) {
        revealBtn.addEventListener('click', function() {
            if (!activeStoryId) return;
            
            fetch(`/rooms/${roomCode}/stories/${activeStoryId}/reveal`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Resposta do reveal:', data);
                if (data.success) {
                    // Marcar como revelado
                    isRevealed = true;
                    
                    // Mostrar resultados imediatamente
                    if (data.votes && data.story) {
                        showRevealedResults(data);
                    }
                    
                    // Se todos votaram o mesmo número, piscar resultados em verde
                    // Aguardar um pouco para garantir que os cards foram criados pelo showRevealedResults
                    setTimeout(() => {
                        console.log('Verificando all_same_vote:', data.all_same_vote);
                        if (data.all_same_vote === true) {
                            console.log('🎉 Todos votaram o mesmo número! Chamando celebrateConsensus...');
                            // Usar a função celebrateConsensus que tem todos os efeitos
                            if (typeof celebrateConsensus === 'function') {
                                celebrateConsensus();
                            } else {
                                console.error('celebrateConsensus não está definida!');
                                // Fallback direto
                                const resultCards = document.querySelectorAll('.result-card');
                                if (resultCards.length > 0) {
                                    resultCards.forEach(card => {
                                        card.classList.remove('celebrate');
                                        void card.offsetHeight; // Force reflow
                                        card.classList.add('celebrate');
                                    });
                                    setTimeout(() => {
                                        resultCards.forEach(card => {
                                            card.classList.remove('celebrate');
                                        });
                                    }, 3200);
                                }
                            }
                        } else {
                            console.log('Votos diferentes ou all_same_vote é false:', data.all_same_vote);
                        }
                    }, 300); // Delay para garantir que o DOM foi atualizado
                    
                    // Atualizar status para sincronizar com outros participantes
                    updateVotesStatus();
                }
            })
            .catch(error => {
                console.error('Erro ao revelar votos:', error);
            });
        });
    }
    
    // Botão de nova estimativa
    const newEstimateBtn = document.getElementById('new-estimate-btn');
    if (newEstimateBtn) {
        newEstimateBtn.addEventListener('click', function() {
            startNewEstimate();
        });
    }
    
    const startEstimateBtn = document.getElementById('start-estimate-btn');
    if (startEstimateBtn) {
        startEstimateBtn.addEventListener('click', function() {
            startNewEstimate();
        });
    }
    
    function startNewEstimate() {
        fetch(`/rooms/${roomCode}/stories/start-new`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || `Erro HTTP: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Recarregar página para todos os usuários verem a nova estimativa
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: data.message || 'Erro ao iniciar nova estimativa',
                    confirmButtonColor: '#667eea'
                });
            }
        })
        .catch(error => {
            console.error('Erro ao iniciar nova estimativa:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: error.message || 'Erro ao iniciar nova estimativa. Tente novamente.',
                confirmButtonColor: '#667eea'
            });
        });
    }
    
    // Atualização automática do status dos votos
    function updateVotesStatus() {
        fetch(`/rooms/${roomCode}/votes-status`)
            .then(response => response.json())
            .then(data => {
                // Verificar se uma nova estimativa foi criada (detectar mudança no count)
                if (data.unrevealed_count !== undefined && data.unrevealed_count !== lastUnrevealedCount) {
                    lastUnrevealedCount = data.unrevealed_count;
                    // Nova estimativa foi criada, recarregar para todos
                    location.reload();
                    return;
                }
                
                if (!data.has_story) {
                    // Se não há história e havia uma antes, recarregar
                    if (activeStoryId) {
                        location.reload();
                    }
                    return;
                }
                
                // Se uma nova estimativa foi criada (sem história ativa antes)
                if (data.has_story && !activeStoryId) {
                    location.reload();
                    return;
                }
                
                // Se a história mudou (ID diferente)
                if (data.story && data.story.id !== activeStoryId) {
                    location.reload();
                    return;
                }
                
                // Atualizar título da história se mudou
                if (data.story && data.story.title) {
                    const titleEl = document.getElementById('story-title');
                    if (titleEl) titleEl.textContent = data.story.title;
                }
                
                // Se foi revelado, atualizar interface
                if (data.story && data.story.is_revealed && !isRevealed) {
                    showRevealedResults(data);
                    // Verificar se todos votaram o mesmo número (vindo do polling)
                    if (data.all_same_vote === true) {
                        console.log('🎉 Todos votaram o mesmo número! Piscando resultados via polling...');
                        console.log('all_same_vote (polling):', data.all_same_vote);
                        setTimeout(() => {
                            if (typeof celebrateConsensus === 'function') {
                                celebrateConsensus();
                            } else if (typeof createConfetti === 'function') {
                                createConfetti();
                            } else {
                                console.error('celebrateConsensus não está definida no polling!');
                                // Tentar acionar diretamente
                                const resultCards = document.querySelectorAll('.result-card');
                                if (resultCards.length > 0) {
                                    resultCards.forEach(card => {
                                        card.classList.add('celebrate');
                                    });
                                    setTimeout(() => {
                                        resultCards.forEach(card => {
                                            card.classList.remove('celebrate');
                                        });
                                    }, 3000);
                                }
                            }
                        }, 800); // Aumentar delay para garantir que os cards foram criados
                    }
                    return;
                }
                
                // Atualizar contador
                const votesCountEl = document.getElementById('votes-count');
                const totalParticipantsEl = document.getElementById('total-participants');
                if (votesCountEl) votesCountEl.textContent = data.total_votes;
                if (totalParticipantsEl) totalParticipantsEl.textContent = data.total_participants;
                
                // Atualizar lista completa de status dos votos
                updateVotesList(data.votes);
                
                // Mostrar botão de revelar se todos votaram e for o criador
                const revealBtn = document.getElementById('reveal-btn');
                if (revealBtn && data.is_creator && data.total_votes === data.total_participants && data.total_participants > 0) {
                    revealBtn.style.display = 'inline-block';
                }
                
                
                // Processar novos arremessos de emoticons
                if (data.emoji_throws && Array.isArray(data.emoji_throws)) {
                    data.emoji_throws.forEach(emojiThrow => {
                        // Criar ID único baseado em timestamp + emoji + participantes
                        const throwId = `${emojiThrow.to_participant_id}_${emojiThrow.emoji}_${Date.now()}`;
                        
                        // Verificar se já foi processado
                        if (!processedEmojiThrows.has(throwId)) {
                            processedEmojiThrows.add(throwId);
                            
                            // Limpar IDs antigos (manter apenas últimos 50)
                            if (processedEmojiThrows.size > 50) {
                                const firstKey = processedEmojiThrows.values().next().value;
                                processedEmojiThrows.delete(firstKey);
                            }
                            
                            // Arremessar emoticon para todos verem
                            if (typeof throwEmoji === 'function') {
                                throwEmoji(emojiThrow.emoji, emojiThrow.to_participant_id);
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar status dos votos:', error);
            });
    }
    
    // Função para atualizar lista de status dos votos
    function updateVotesList(votes) {
        const votesList = document.getElementById('votes-list');
        if (!votesList) return;
        
        // Criar um mapa dos participantes atuais na lista
        const currentParticipants = new Map();
        votesList.querySelectorAll('.participant-card').forEach(card => {
            const participantId = card.getAttribute('data-participant-id');
            if (participantId) {
                currentParticipants.set(participantId, card);
            }
        });
        
        // Criar um mapa dos votos recebidos
        const votesMap = new Map();
        votes.forEach(vote => {
            votesMap.set(vote.id.toString(), vote);
        });
        
        // Atualizar ou adicionar cada participante que votou ou pode votar
        votes.forEach(vote => {
            let card = currentParticipants.get(vote.id.toString());
            
            if (!card) {
                // Criar novo card se não existir
                card = document.createElement('div');
                card.className = 'participant-card emoji-target';
                card.setAttribute('data-participant-id', vote.id);
                card.setAttribute('data-participant-name', vote.name);
                votesList.appendChild(card);
            }
            
            // Atualizar nome (caso tenha mudado)
            let nameEl = card.querySelector('.participant-name');
            if (!nameEl) {
                nameEl = document.createElement('div');
                nameEl.className = 'participant-name';
                card.insertBefore(nameEl, card.firstChild);
            }
            nameEl.textContent = vote.name;
            
            // Atualizar status do voto
            let voteStatus = card.querySelector('.participant-vote');
            if (!voteStatus) {
                voteStatus = document.createElement('div');
                voteStatus.className = 'participant-vote';
                card.appendChild(voteStatus);
            }
            
            if (vote.has_voted) {
                voteStatus.innerHTML = '<span style="color: #28a745; font-weight: bold;">✓ Votou</span>';
            } else {
                voteStatus.innerHTML = '<span style="color: #dc3545;">⏳ Aguardando...</span>';
            }
            
            // Adicionar emoji picker se não for o próprio participante
            if (participantId && participantId != vote.id) {
                let emojiPicker = card.querySelector('.emoji-picker');
                if (!emojiPicker) {
                    emojiPicker = document.createElement('div');
                    emojiPicker.className = 'emoji-picker';
                    emojiPicker.style.display = 'none';
                    emojiPicker.innerHTML = `
                        <div class="emoji-options">
                            <button class="emoji-btn" data-emoji="🎯" title="Arremessar">🎯</button>
                            <button class="emoji-btn" data-emoji="🔥" title="Arremessar">🔥</button>
                            <button class="emoji-btn" data-emoji="💥" title="Arremessar">💥</button>
                            <button class="emoji-btn" data-emoji="⚡" title="Arremessar">⚡</button>
                            <button class="emoji-btn" data-emoji="🎉" title="Arremessar">🎉</button>
                            <button class="emoji-btn" data-emoji="🚀" title="Arremessar">🚀</button>
                            <button class="emoji-btn" data-emoji="⏰" title="Arremessar">⏰</button>
                            <button class="emoji-btn" data-emoji="💩" title="Arremessar">💩</button>
                        </div>
                    `;
                    card.appendChild(emojiPicker);
                }
            }
        });
        
        // Remover participantes que não estão mais na lista de votos
        // Mas NUNCA remover o participante atual, mesmo que não esteja no mapa
        currentParticipants.forEach((card, participantIdStr) => {
            // Não remover se for o participante atual
            if (participantId && participantId.toString() === participantIdStr) {
                return;
            }
            
            if (!votesMap.has(participantIdStr)) {
                card.remove();
            }
        });
        
        // Re-inicializar sistema de emoticons após atualização
        if (typeof initEmojiSystem === 'function') {
            // Remover flags de inicialização para re-inicializar
            votesList.querySelectorAll('.emoji-target').forEach(card => {
                delete card.dataset.emojiInitialized;
            });
            initEmojiSystem();
        }
    }
    
    // Função para atualizar lista de participantes
    function updateParticipantsList(participants) {
        const participantsList = document.getElementById('all-participants-list');
        const participantsCount = document.getElementById('participants-count');
        
        if (!participantsList) return;
        
        // Atualizar contador
        if (participantsCount) {
            participantsCount.textContent = participants.length;
        }
        
        // Limpar lista atual
        participantsList.innerHTML = '';
        
        // Adicionar cada participante
        participants.forEach(p => {
            const participantCard = document.createElement('div');
            participantCard.className = 'participant-card emoji-target';
            participantCard.setAttribute('data-participant-id', p.id);
            participantCard.setAttribute('data-participant-name', p.name);
            
            let nameHtml = `<div class="participant-name">${p.name}`;
            nameHtml += `</div>`;
            
            // Adicionar emoji picker se não for o próprio participante
            let emojiPickerHtml = '';
            if (participantId && participantId !== p.id) {
                emojiPickerHtml = `
                    <div class="emoji-picker" style="display: none;">
                        <div class="emoji-options">
                            <button class="emoji-btn" data-emoji="🎯" title="Arremessar">🎯</button>
                            <button class="emoji-btn" data-emoji="🔥" title="Arremessar">🔥</button>
                            <button class="emoji-btn" data-emoji="💥" title="Arremessar">💥</button>
                            <button class="emoji-btn" data-emoji="⚡" title="Arremessar">⚡</button>
                            <button class="emoji-btn" data-emoji="🎉" title="Arremessar">🎉</button>
                            <button class="emoji-btn" data-emoji="🚀" title="Arremessar">🚀</button>
                            <button class="emoji-btn" data-emoji="⏰" title="Arremessar">⏰</button>
                            <button class="emoji-btn" data-emoji="💩" title="Arremessar">💩</button>
                        </div>
                    </div>
                `;
            }
            
            participantCard.innerHTML = nameHtml + emojiPickerHtml;
            participantsList.appendChild(participantCard);
        });
        
        // Re-inicializar sistema de emoticons após atualização
        if (typeof initEmojiSystem === 'function') {
            // Remover flags de inicialização para re-inicializar
            document.querySelectorAll('.emoji-target').forEach(card => {
                delete card.dataset.emojiInitialized;
            });
            initEmojiSystem();
        }
    }
    
    function showRevealedResults(data) {
        console.log('showRevealedResults chamado com:', data);
        const votingArea = document.getElementById('voting-area');
        if (!votingArea) {
            console.error('voting-area não encontrado!');
            return;
        }
        
        // Esconder área de votação
        const voteCardsContainer = document.getElementById('vote-cards-container');
        const votesStatus = document.getElementById('votes-status');
        const revealBtn = document.getElementById('reveal-btn');
        if (voteCardsContainer) voteCardsContainer.style.display = 'none';
        if (votesStatus) votesStatus.style.display = 'none';
        if (revealBtn) revealBtn.style.display = 'none';
        
        // Criar área de resultados
        let resultsContainer = document.getElementById('results-container');
        if (!resultsContainer) {
            resultsContainer = document.createElement('div');
            resultsContainer.id = 'results-container';
            votingArea.appendChild(resultsContainer);
        }
        
        // Adicionar classe celebrate se todos votaram igual
        const celebrateClass = data.all_same_vote ? ' celebrate' : '';
        console.log('showRevealedResults - all_same_vote:', data.all_same_vote, 'celebrateClass:', celebrateClass);
        
        resultsContainer.innerHTML = `
            <div class="results">
                <div class="result-card${celebrateClass}">
                    <div class="result-value" id="average-vote">${data.story.average_vote || 'N/A'}</div>
                    <div class="result-label">Média</div>
                </div>
                <div class="result-card${celebrateClass}">
                    <div class="result-value" id="total-votes-count">${data.story.votes_count}</div>
                    <div class="result-label">Votos</div>
                </div>
            </div>
            
            <h3 style="margin-top: 16px;">Votos dos Participantes</h3>
            <div class="participants-list" id="revealed-votes-list">
                ${data.votes.map(v => `
                    <div class="participant-card emoji-target" data-participant-id="${v.id}" data-participant-name="${v.name}">
                        <div class="participant-name">${v.name}</div>
                        <div class="participant-vote">
                                    Voto: <strong style="color: #667eea; font-size: 16px;">${v.vote_value || 'Não votou'}</strong>
                        </div>
                        ${participantId && participantId !== v.id ? `
                            <div class="emoji-picker" style="display: none;">
                                <div class="emoji-options">
                                    <button class="emoji-btn" data-emoji="🎯" title="Arremessar">🎯</button>
                                    <button class="emoji-btn" data-emoji="🔥" title="Arremessar">🔥</button>
                                    <button class="emoji-btn" data-emoji="💥" title="Arremessar">💥</button>
                                    <button class="emoji-btn" data-emoji="⚡" title="Arremessar">⚡</button>
                                    <button class="emoji-btn" data-emoji="🎉" title="Arremessar">🎉</button>
                                    <button class="emoji-btn" data-emoji="🚀" title="Arremessar">🚀</button>
                                    <button class="emoji-btn" data-emoji="⏰" title="Arremessar">⏰</button>
                                    <button class="emoji-btn" data-emoji="💩" title="Arremessar">💩</button>
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `).join('')}
            </div>
                    ${data.is_creator ? '<button id="new-estimate-btn" class="btn btn-secondary" style="margin-top: 12px;">Nova Estimativa</button>' : ''}
        `;
        
        // Adicionar evento ao botão de nova estimativa
        const newEstimateBtn = document.getElementById('new-estimate-btn');
        if (newEstimateBtn) {
            newEstimateBtn.addEventListener('click', startNewEstimate);
        }
        
        // Re-inicializar sistema de emoticons após atualização dinâmica
        if (typeof initEmojiSystem === 'function') {
            // Remover flags de inicialização para re-inicializar
            document.querySelectorAll('.emoji-target').forEach(card => {
                delete card.dataset.emojiInitialized;
            });
            initEmojiSystem();
        }
        
        // Se todos votaram igual, garantir que a animação seja acionada
        if (data.all_same_vote === true) {
            console.log('showRevealedResults - Todos votaram igual, acionando animação...');
            console.log('showRevealedResults - Dados:', data);
            
            // Múltiplas tentativas para garantir que funcione
            const tryCelebration = (attempt = 1) => {
                console.log(`Tentativa ${attempt} de celebrar consenso...`);
                
                if (typeof celebrateConsensus === 'function') {
                    celebrateConsensus();
                    console.log('celebrateConsensus chamada com sucesso');
                } else {
                    console.error('celebrateConsensus não está definida!');
                    // Fallback: adicionar classe diretamente
                    const resultCards = document.querySelectorAll('.result-card');
                    console.log('Fallback: Cards encontrados:', resultCards.length);
                    if (resultCards.length > 0) {
                        resultCards.forEach(card => {
                            card.classList.remove('celebrate');
                            void card.offsetHeight; // Force reflow
                            card.classList.add('celebrate');
                            card.style.animation = 'resultBlink 0.3s ease-in-out 10';
                        });
                        setTimeout(() => {
                            resultCards.forEach(card => {
                                card.classList.remove('celebrate');
                                card.style.animation = '';
                            });
                        }, 3000);
                    } else if (attempt < 5) {
                        // Tentar novamente se não encontrou os cards
                        setTimeout(() => tryCelebration(attempt + 1), 200);
                    }
                }
            };
            
            // Primeira tentativa após 300ms
            setTimeout(() => tryCelebration(1), 300);
            // Segunda tentativa após 600ms (backup)
            setTimeout(() => tryCelebration(2), 600);
        }
    }
    
    // Função para arremessar emoticon (usada localmente e via polling)
    // Deve estar disponível globalmente para o polling funcionar
    function throwEmoji(emoji, toParticipantId, targetCard) {
        // Se targetCard não foi passado, encontrar pelo ID
        if (!targetCard) {
            targetCard = document.querySelector(`[data-participant-id="${toParticipantId}"]`);
        }
        
        if (!targetCard) return;
        
        const targetRect = targetCard.getBoundingClientRect();
        const targetX = targetRect.left + targetRect.width / 2;
        const targetY = targetRect.top + targetRect.height / 2;
        
        // Posição inicial (centro da tela)
        const startX = window.innerWidth / 2;
        const startY = window.innerHeight / 2;
        
        // Criar elemento voando
        const flyingEmoji = document.createElement('div');
        flyingEmoji.className = 'flying-emoji';
        flyingEmoji.textContent = emoji;
        flyingEmoji.style.left = startX + 'px';
        flyingEmoji.style.top = startY + 'px';
        document.body.appendChild(flyingEmoji);
        
        // Animar voo
        const startTime = Date.now();
        const duration = 800;
        const deltaX = targetX - startX;
        const deltaY = targetY - startY;
        
        function animate() {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 3);
            const currentX = startX + (deltaX * ease);
            const currentY = startY + (deltaY * ease);
            const scale = 1 + (0.5 * progress);
            const rotation = 360 * progress;
            
            flyingEmoji.style.left = currentX + 'px';
            flyingEmoji.style.top = currentY + 'px';
            flyingEmoji.style.transform = `translate(-50%, -50%) scale(${scale}) rotate(${rotation}deg)`;
            flyingEmoji.style.opacity = 1 - (progress * 0.2);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                // Efeito de hit no card
                targetCard.classList.add('emoji-hit');
                setTimeout(() => {
                    targetCard.classList.remove('emoji-hit');
                }, 400);
                
                // Remover emoticon
                if (flyingEmoji.parentNode) {
                    flyingEmoji.parentNode.removeChild(flyingEmoji);
                }
            }
        }
        
        requestAnimationFrame(animate);
    }
    
    // Sistema de arremesso de emoticons
    <?php if($participant): ?>
    function initEmojiSystem() {
            document.querySelectorAll('.emoji-target').forEach(card => {
                const picker = card.querySelector('.emoji-picker');
                if (!picker || card.dataset.emojiInitialized === 'true') return;
                
                card.dataset.emojiInitialized = 'true';
                
                // Função para posicionar o picker responsivamente
                function positionPicker() {
                    const cardRect = card.getBoundingClientRect();
                    const pickerRect = picker.getBoundingClientRect();
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    const padding = 10; // Espaçamento mínimo da borda da tela
                    
                    // Resetar transform
                    picker.style.transform = '';
                    picker.style.top = '';
                    picker.style.bottom = '';
                    picker.style.left = '';
                    picker.style.right = '';
                    
                    // Calcular posição horizontal
                    const cardCenterX = cardRect.left + (cardRect.width / 2);
                    const pickerWidth = pickerRect.width || 200; // Estimativa se ainda não renderizado
                    const halfPickerWidth = pickerWidth / 2;
                    
                    let leftPosition;
                    let transformX = '';
                    
                    // Tentar centralizar no card primeiro
                    leftPosition = cardCenterX;
                    transformX = 'translateX(-50%)';
                    
                    // Verificar se ultrapassa à esquerda
                    const leftBound = leftPosition - halfPickerWidth;
                    if (leftBound < padding) {
                        leftPosition = cardRect.left + padding;
                        transformX = 'translateX(0)';
                    }
                    // Verificar se ultrapassa à direita
                    else {
                        const rightBound = leftPosition + halfPickerWidth;
                        if (rightBound > viewportWidth - padding) {
                            leftPosition = viewportWidth - pickerWidth - padding;
                            transformX = 'translateX(0)';
                        }
                    }
                    
                    // Calcular posição vertical
                    const spaceAbove = cardRect.top;
                    const spaceBelow = viewportHeight - cardRect.bottom;
                    const pickerHeight = pickerRect.height || 50; // Estimativa
                    const verticalOffset = 8; // Espaçamento do card
                    
                    let topPosition, bottomPosition;
                    
                    // Preferir acima, mas verificar se há espaço
                    if (spaceAbove >= pickerHeight + verticalOffset + padding) {
                        // Posicionar acima
                        picker.style.top = `-${pickerHeight + verticalOffset}px`;
                        picker.style.bottom = 'auto';
                    } else if (spaceBelow >= pickerHeight + verticalOffset + padding) {
                        // Posicionar abaixo
                        picker.style.top = `${cardRect.height + verticalOffset}px`;
                        picker.style.bottom = 'auto';
                    } else {
                        // Não há espaço suficiente, usar o lado com mais espaço
                        if (spaceAbove > spaceBelow) {
                            picker.style.top = `-${Math.min(pickerHeight + verticalOffset, spaceAbove - padding)}px`;
                            picker.style.bottom = 'auto';
                        } else {
                            picker.style.top = `${cardRect.height + verticalOffset}px`;
                            picker.style.bottom = 'auto';
                        }
                    }
                    
                    // Aplicar posição horizontal
                    picker.style.left = `${leftPosition}px`;
                    if (transformX) {
                        picker.style.transform = transformX;
                    }
                }
                
                // Mostrar picker ao hover e posicionar responsivamente
                card.addEventListener('mouseenter', function() {
                    picker.style.display = 'block';
                    // Aguardar um frame para o picker ser renderizado e calcular dimensões corretas
                    requestAnimationFrame(() => {
                        positionPicker();
                    });
                });
                
                let hideTimeout;
                
                card.addEventListener('mouseleave', function(e) {
                    const relatedTarget = e.relatedTarget;
                    if (relatedTarget && (picker.contains(relatedTarget) || card.contains(relatedTarget))) {
                        return;
                    }
                    hideTimeout = setTimeout(() => {
                        if (!picker.matches(':hover') && !card.matches(':hover')) {
                            picker.style.display = 'none';
                        }
                    }, 300);
                });
                
                picker.addEventListener('mouseenter', function() {
                    clearTimeout(hideTimeout);
                    picker.style.display = 'block';
                    // Recalcular posição ao entrar no picker
                    requestAnimationFrame(() => {
                        positionPicker();
                    });
                });
                
                // Recalcular posição ao redimensionar a janela
                window.addEventListener('resize', function() {
                    if (picker.style.display === 'block') {
                        positionPicker();
                    }
                });
                
                picker.addEventListener('mouseleave', function() {
                    hideTimeout = setTimeout(() => {
                        if (!card.matches(':hover')) {
                            picker.style.display = 'none';
                        }
                    }, 300);
                });
                
                // Botões de emoticon
                picker.querySelectorAll('.emoji-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const emoji = this.dataset.emoji;
                        const toParticipantId = card.dataset.participantId;
                        const toParticipantName = card.dataset.participantName;
                        
                        const targetRect = card.getBoundingClientRect();
                        const targetX = targetRect.left + targetRect.width / 2;
                        const targetY = targetRect.top + targetRect.height / 2;
                        
                        // Enviar para backend primeiro
                        fetch(`/rooms/${roomCode}/emoji-throw`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                to_participant_id: toParticipantId,
                                emoji: emoji
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Arremessar localmente (todos verão via polling)
                                if (typeof throwEmoji === 'function') {
                                    throwEmoji(emoji, toParticipantId, card);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao arremessar emoticon:', error);
                        });
                        
                        picker.style.display = 'none';
                    });
                });
            });
        }
        
        // Inicializar sistema de emoticons
        initEmojiSystem();
    <?php endif; ?>
    
    // Atualizar a cada 5 segundos (otimizado para reduzir carga no servidor)
    // Intervalo aumentado de 2s para 5s para reduzir requisições em 60%
    setInterval(updateVotesStatus, 5000);
    updateVotesStatus(); // Executar imediatamente
    
    // Sistema de heartbeat para manter participante ativo
    <?php if($participant): ?>
    function sendHeartbeat() {
        fetch(`/rooms/${roomCode}/heartbeat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        }).catch(error => {
            console.error('Erro ao enviar heartbeat:', error);
        });
    }
    
    // Enviar heartbeat a cada 30 segundos (otimizado - aumentado de 10s para 30s)
    // Reduz requisições de heartbeat em 66%
    setInterval(sendHeartbeat, 30000);
    sendHeartbeat(); // Executar imediatamente
    
    // NOTA: Não usamos beforeunload para remover o participante porque:
    // 1. É disparado também em reloads, causando remoção indevida
    // 2. Não é confiável em todos os navegadores
    // 3. O sistema de heartbeat + limpeza automática (30s) já cuida da remoção de participantes inativos
    <?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/rooms/show.blade.php ENDPATH**/ ?>