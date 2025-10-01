<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('libro_id');
            $table->unsignedBigInteger('user_id');
            $table->date('fecha_prestamo');
            $table->date('fecha_devolucion');
            $table->date('fecha_devuelto')->nullable();
            $table->enum('estado', ['prestado', 'devuelto', 'vencido'])->default('prestado');
            $table->timestamps();

            $table->foreign('libro_id')->references('id')->on('libros')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
