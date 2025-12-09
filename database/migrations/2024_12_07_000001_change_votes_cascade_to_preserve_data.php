<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Buscar o nome da foreign key atual
        $result = DB::selectOne("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'votes' 
            AND COLUMN_NAME = 'participant_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");
        
        // Dropar a foreign key atual se existir
        if ($result && isset($result->CONSTRAINT_NAME)) {
            $keyName = $result->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE votes DROP FOREIGN KEY `{$keyName}`");
        }
        
        // Tornar participant_id nullable usando SQL raw
        DB::statement('ALTER TABLE votes MODIFY participant_id BIGINT UNSIGNED NULL');
        
        // Recriar foreign key com onDelete('set null') para preservar votos
        DB::statement('ALTER TABLE votes ADD CONSTRAINT votes_participant_id_foreign 
            FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE SET NULL');
    }

    public function down(): void
    {
        // Buscar o nome da foreign key atual
        $result = DB::selectOne("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'votes' 
            AND COLUMN_NAME = 'participant_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");
        
        // Dropar a foreign key atual se existir
        if ($result && isset($result->CONSTRAINT_NAME)) {
            $keyName = $result->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE votes DROP FOREIGN KEY `{$keyName}`");
        }
        
        // Tornar participant_id not nullable novamente
        // Primeiro, garantir que não há valores NULL (remover votos órfãos)
        DB::statement('DELETE FROM votes WHERE participant_id IS NULL');
        
        DB::statement('ALTER TABLE votes MODIFY participant_id BIGINT UNSIGNED NOT NULL');
        
        // Recriar foreign key com cascade (comportamento original)
        DB::statement('ALTER TABLE votes ADD CONSTRAINT votes_participant_id_foreign 
            FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE');
    }
};
