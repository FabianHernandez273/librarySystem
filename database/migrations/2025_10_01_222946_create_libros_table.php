<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('libros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->unsignedBigInteger('autor_id')->nullable();
            $table->unsignedBigInteger('genero_id')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->integer('copias_totales')->default(1);
            $table->integer('copias_disponibles')->default(1);
            $table->text('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('autor_id')->references('id')->on('autores')->onDelete('set null');
            $table->foreign('genero_id')->references('id')->on('generos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libros');
    }
};
