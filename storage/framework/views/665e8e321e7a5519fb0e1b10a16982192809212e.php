<?php $__env->startSection('title', 'Criar Sala - Planning Poker'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <h1>Criar Nova Sala</h1>
    
    <form method="POST" action="<?php echo e(route('rooms.store')); ?>" id="create-room-form">
        <?php echo csrf_field(); ?>
        
        <div class="form-group">
            <label for="room_name">Nome da Sala</label>
            <input 
                type="text" 
                id="room_name" 
                name="name" 
                required 
                placeholder="Ex: Sprint 2024 - Backend"
                value="<?php echo e(old('name')); ?>"
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
                value="<?php echo e(old('creator_name', request()->cookie('participant_name'))); ?>"
            >
        </div>
        
        <button type="submit" class="btn">Criar Sala</button>
        <a href="<?php echo e(route('welcome')); ?>" class="btn btn-secondary" style="margin-left: 10px;">Voltar</a>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/rooms/create.blade.php ENDPATH**/ ?>