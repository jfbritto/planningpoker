<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Dropar a foreign key atual
            $table->dropForeign(['participant_id']);
            
            // Tornar participant_id nullable para preservar votos mesmo quando participantes são removidos
            $table->unsignedBigInteger('participant_id')->nullable()->change();
            
            // Recriar foreign key com onDelete('set null') para preservar votos
            $table->foreign('participant_id')
                ->references('id')
                ->on('participants')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            // Dropar a foreign key atual
            $table->dropForeign(['participant_id']);
            
            // Tornar participant_id not nullable novamente
            $table->unsignedBigInteger('participant_id')->nullable(false)->change();
            
            // Recriar foreign key com cascade (comportamento original)
            $table->foreign('participant_id')
                ->references('id')
                ->on('participants')
                ->onDelete('cascade');
        });
    }
};
