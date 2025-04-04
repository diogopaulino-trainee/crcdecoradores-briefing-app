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
        Schema::create('propostas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero')->unique();
            $table->date('data_da_proposta');
            // Apenas entidades do tipo 'cliente'
            $table->foreignId('cliente_id')->constrained('entidades')->onDelete('cascade');
            $table->date('validade')->nullable();
            $table->enum('estado', ['Rascunho', 'Fechado'])->default('Rascunho');
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propostas');
    }
};
