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
        Schema::create('player_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')
                ->constrained('players')
                ->onDelete('cascade'); // при удалении игрока удаляются изображения
            $table->text('filename')->nullable();  // оригинальное имя файла
            $table->longText('base64')->nullable(); // содержимое изображения в base64
            $table->string('mime_type')->nullable(); // mime тип
            $table->unsignedInteger('size')->nullable(); // размер в байтах (для удобства)
            $table->timestamps();
            $table->softDeletes();

            $table->index('player_id'); // ускоряет выборку по игроку
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_images');
    }
};
