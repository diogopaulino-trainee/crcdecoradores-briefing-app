<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Pais;
use App\Models\Funcao;
use App\Models\Iva;
use App\Models\Empresa;
use App\Models\Entidade;
use App\Models\Contacto;
use App\Models\Artigo;
use App\Models\Proposta;
use App\Models\Encomenda;
use App\Models\OrdemTrabalho;
use App\Models\FaturaFornecedor;
use Spatie\Permission\Models\Role;

class SeederGeral extends Seeder
{
    public function run(): void
    {
        // Países
        $portugal = Pais::create(['nome' => 'Portugal', 'codigo' => 'PT']);
        $espanha = Pais::create(['nome' => 'Espanha', 'codigo' => 'ES']);

        // Funções
        Funcao::insert([
            ['nome' => 'Administrador', 'descricao' => 'Acesso total ao sistema'],
            ['nome' => 'Gestor', 'descricao' => 'Responsável pela gestão de operações'],
            ['nome' => 'Colaborador', 'descricao' => 'Acesso limitado'],
        ]);

        // IVAs
        Iva::create([
            'nome' => 'IVA 23%',
            'percentagem' => 23,
        ]);
        
        Iva::create([
            'nome' => 'IVA 6%',
            'percentagem' => 6,
        ]);
        
        Iva::create([
            'nome' => 'IVA 0%',
            'percentagem' => 0,
        ]);

        // Empresa
        Empresa::create([
            'logotipo' => 'logo_crc.png',
            'nome' => 'CRC Decoradores',
            'morada' => 'Rua das Flores 123',
            'codigo_postal' => '1000-000',
            'localidade' => 'Lisboa',
            'numero_contribuinte' => '501234567',
        ]);

        // Entidades
        $cliente = Entidade::create([
            'tipo' => 'cliente',
            'numero' => 1,
            'nif' => '123456789',
            'nome' => 'Cliente Exemplo',
            'morada' => 'Rua do Cliente',
            'codigo_postal' => '4000-001',
            'localidade' => 'Porto',
            'pais_id' => $portugal->id,
            'telefone' => '222111333',
            'telemovel' => '912345678',
            'website' => 'https://cliente.com',
            'email' => 'cliente@exemplo.com',
            'consentimento_rgpd' => 'sim',
            'observacoes' => 'Cliente habitual',
            'estado' => 'ativo',
        ]);

        $fornecedor = Entidade::create([
            'tipo' => 'fornecedor',
            'numero' => 2,
            'nif' => '987654321',
            'nome' => 'Fornecedor Exemplo',
            'morada' => 'Rua do Fornecedor',
            'codigo_postal' => '5000-002',
            'localidade' => 'Braga',
            'pais_id' => $espanha->id,
            'telefone' => '223334455',
            'telemovel' => '913456789',
            'website' => 'https://fornecedor.com',
            'email' => 'fornecedor@exemplo.com',
            'consentimento_rgpd' => 'nao',
            'observacoes' => 'Fornecedor externo',
            'estado' => 'ativo',
        ]);

        // Contactos
        Contacto::create([
            'numero' => 1,
            'entidade_id' => $cliente->id,
            'primeiro_nome' => 'Ana',
            'apelido' => 'Silva',
            'funcao' => 'Gestor',
            'telefone' => '222111999',
            'telemovel' => '919999888',
            'email' => 'ana.silva@cliente.com',
            'consentimento_rgpd' => 'sim',
            'observacoes' => 'Pessoa de contacto principal',
            'estado' => 'ativo',
        ]);

        $iva23 = Iva::where('percentagem', 23)->first();

        // Artigos
        $artigo = Artigo::create([
            'referencia' => 'ART123',
            'nome' => 'Cadeira de Escritório',
            'descricao' => 'Cadeira ergonómica preta',
            'preco' => 120.00,
            'iva_id' => $iva23->id,
            'foto' => 'cadeira.jpg',
            'observacoes' => 'Top seller',
            'estado' => 'ativo',
        ]);

        // Propostas
        $proposta = Proposta::create([
            'numero' => 1001,
            'data_da_proposta' => now()->subDays(5),
            'cliente_id' => $cliente->id,
            'validade' => now()->addDays(25),
            'estado' => 'fechado',
            'valor_total' => 147.60,
        ]);

        // Encomenda cliente
        $encomendaCliente = Encomenda::create([
            'tipo' => 'cliente',
            'numero' => 2001,
            'data_da_proposta' => $proposta->data_da_proposta,
            'cliente_id' => $cliente->id,
            'estado' => 'fechado',
            'valor_total' => $proposta->valor_total,
        ]);

        // Encomenda fornecedor
        $encomendaFornecedor = Encomenda::create([
            'tipo' => 'fornecedor',
            'numero' => 2002,
            'data_da_proposta' => now()->subDays(2),
            'cliente_id' => $fornecedor->id,
            'estado' => 'fechado',
            'valor_total' => 99.90,
        ]);

        // Ordem de Trabalho
        OrdemTrabalho::create([
            'numero' => 3001,
            'data_da_ordem' => now(),
            'entidade_id' => $cliente->id,
            'descricao' => 'Montagem de cadeiras',
            'estado' => 'ativo',
        ]);

        // Fatura Fornecedor
        FaturaFornecedor::create([
            'numero' => 4001,
            'data_da_fatura' => now()->subDays(1),
            'data_de_vencimento' => now()->addDays(14),
            'fornecedor_id' => $fornecedor->id,
            'encomenda_fornecedor_id' => $encomendaFornecedor->id,
            'valor_total' => 99.90,
            'documento' => 'fatura4001.pdf',
            'comprovativo_pagamento' => 'comprovativo4001.pdf',
            'estado' => 'paga',
        ]);
    }
}
