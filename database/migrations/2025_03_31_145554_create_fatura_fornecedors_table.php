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
        Schema::create('faturas_fornecedores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero')->unique();
            $table->date('data_da_fatura');
            $table->date('data_de_vencimento');
            $table->foreignId('fornecedor_id')->constrained('entidades')->onDelete('cascade');
            $table->foreignId('encomenda_fornecedor_id')->nullable()->constrained('encomendas')->onDelete('cascade');
            $table->decimal('valor_total', 10, 2);
            // Campos para ficheiros anexos – podem ser armazenados como strings (caminhos)
            $table->string('documento')->nullable();
            $table->string('comprovativo_pagamento')->nullable();
            $table->enum('estado', ['pendente', 'paga'])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fatura_fornecedors');
    }
};
