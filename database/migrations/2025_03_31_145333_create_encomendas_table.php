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
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['cliente', 'fornecedor']);
            $table->unsignedBigInteger('numero')->unique();
            $table->date('data_da_proposta');
            $table->foreignId('cliente_id')->constrained('entidades')->onDelete('cascade');
            $table->enum('estado', ['rascunho', 'fechado'])->default('rascunho');
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
