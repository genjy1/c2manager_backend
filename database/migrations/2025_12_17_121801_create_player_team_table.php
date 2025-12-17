<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('player_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')
                ->constrained('players')
                ->onDelete('cascade');
            $table->foreignId('team_id')
                ->constrained('teams')
                ->onDelete('cascade');
            $table->string('position')->nullable(); // Позиция игрока в команде
            $table->date('joined_at')->nullable(); // Дата вступления в команду
            $table->date('left_at')->nullable(); // Дата выхода из команды
            $table->boolean('is_captain')->default(false); // Капитан команды
            $table->timestamps();

            $table->unique(['player_id', 'team_id']);
            $table->index(['team_id', 'is_captain']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_team');
    }
};
