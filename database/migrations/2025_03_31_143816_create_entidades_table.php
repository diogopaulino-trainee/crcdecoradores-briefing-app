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
        Schema::create('entidades', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['cliente', 'fornecedor']);
            $table->unsignedBigInteger('numero')->unique();
            $table->string('nif')->unique();
            $table->string('nif_hash')->nullable()->index();
            $table->string('nome');
            $table->string('morada')->nullable();
            $table->string('codigo_postal', 8)->nullable();
            $table->string('localidade')->nullable();
            $table->foreignId('pais_id')->nullable()->constrained('paises')->onDelete('set null');
            $table->string('telefone')->nullable();
            $table->string('telemovel')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->enum('consentimento_rgpd', ['Sim', 'Não'])->default('Não');
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['Ativo', 'Inativo'])->default('Ativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entidades');
    }
};
