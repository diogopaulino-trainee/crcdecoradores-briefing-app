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
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('numero')->unique();
            $table->foreignId('entidade_id')->constrained('entidades')->onDelete('cascade');
            $table->string('primeiro_nome');
            $table->string('apelido');
            $table->string('funcao')->nullable();
            $table->string('telefone')->nullable();
            $table->string('telemovel')->nullable();
            $table->string('email')->nullable();
            $table->enum('consentimento_rgpd', ['sim', 'nao'])->default('nao');
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
