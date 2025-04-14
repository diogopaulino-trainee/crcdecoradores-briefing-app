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
            $table->foreignId('funcao_id')->nullable()->constrained('funcoes')->onDelete('set null');
            $table->string('telefone')->nullable();
            $table->string('telemovel')->nullable();
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
        Schema::dropIfExists('contactos');
    }
};
