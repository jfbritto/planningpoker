<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->string('value'); // Pode ser números ou "?" para incerteza
            $table->timestamps();
            
            $table->unique(['story_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};


