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
        Schema::create('linhas_propostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposta_id')->constrained()->onDelete('cascade');
            $table->foreignId('artigo_id')->constrained()->onDelete('restrict');
            $table->decimal('quantidade', 10, 2);
            $table->decimal('preco_unitario', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linhas_propostas');
    }
};
