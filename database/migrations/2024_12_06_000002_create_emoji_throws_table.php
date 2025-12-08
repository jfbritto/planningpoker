<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emoji_throws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_participant_id')->constrained('participants')->onDelete('cascade');
            $table->foreignId('to_participant_id')->constrained('participants')->onDelete('cascade');
            $table->string('emoji', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emoji_throws');
    }
};


