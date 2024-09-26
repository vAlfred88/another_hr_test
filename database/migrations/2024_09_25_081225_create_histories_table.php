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
        Schema::create('histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('model_id');         // ID измененной модели (пользователя)
            $table->string('model_name');     // Название модели (User)
            $table->json('before')->nullable(); // Данные до изменений
            $table->json('after')->nullable();  // Данные после изменений
            $table->enum('action', ['created', 'updated', 'deleted', 'restored']); // Тип действия
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
