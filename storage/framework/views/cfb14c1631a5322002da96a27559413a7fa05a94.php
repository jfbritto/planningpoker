<?php $__env->startSection('title', 'Planning Poker - Início'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <h1 style="margin-bottom: 4px;">🎴 Planning Poker</h1>
            <p style="color: #666; font-size: 13px; margin: 0;">
                Sistema simples e objetivo para estimativas ágeis
            </p>
        </div>
        <a href="<?php echo e(route('rooms.create')); ?>" class="btn">
            Criar Nova Sala
        </a>
    </div>
</div>

<?php if($rooms && $rooms->count() > 0): ?>
    <div class="card">
        <h2 style="margin-bottom: 16px;">Salas Ativas (<?php echo e($rooms->count()); ?>)</h2>
        <div class="rooms-list">
            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="room-item">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <h3 style="margin: 0; font-size: 16px;"><?php echo e($room->name); ?></h3>
                            <span style="font-size: 11px; color: #999; background: #f3f4f6; padding: 2px 6px; border-radius: 10px; font-family: monospace;">
                                <?php echo e($room->code); ?>

                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #666;">
                            <span>👥 <?php echo e($room->participants_count); ?> participante(s)</span>
                            <span>•</span>
                            <span>🕐 <?php echo e($room->updated_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('rooms.show', $room->code)); ?>" class="btn" style="padding: 6px 16px; font-size: 13px;">
                        Entrar
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php else: ?>
    <div class="card" style="text-align: center; padding: 40px;">
        <p style="color: #666; font-size: 14px; margin-bottom: 16px;">Nenhuma sala ativa no momento</p>
        <a href="<?php echo e(route('rooms.create')); ?>" class="btn">
            Criar Primeira Sala
        </a>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="font-size: 16px; margin-bottom: 12px;">Como usar:</h2>
    <ol style="line-height: 1.8; color: #666; font-size: 13px; padding-left: 20px;">
        <li>Crie uma nova sala ou entre em uma sala existente</li>
        <li>Os participantes votam usando os cartões de Planning Poker</li>
        <li>Revele os votos para ver os resultados</li>
        <li>Discuta as diferenças e chegue a um consenso</li>
    </ol>
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
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/welcome.blade.php ENDPATH**/ ?>