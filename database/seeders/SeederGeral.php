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
        $frança = Pais::create(['nome' => 'França', 'codigo' => 'FR']);
        $alemanha = Pais::create(['nome' => 'Alemanha', 'codigo' => 'DE']);
        $italia = Pais::create(['nome' => 'Itália', 'codigo' => 'IT']);
        $belgium = Pais::create(['nome' => 'Bélgica', 'codigo' => 'BE']);
        $holanda = Pais::create(['nome' => 'Holanda', 'codigo' => 'NL']);
        $austria = Pais::create(['nome' => 'Áustria', 'codigo' => 'AT']);
        $grecia = Pais::create(['nome' => 'Grécia', 'codigo' => 'GR']);
        

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
        $cliente = null;
        foreach (range(1, 20) as $i) {
            $nif = 'PT' . str_pad($i, 9, '1', STR_PAD_LEFT);
            $cliente = Entidade::create([
                'tipo' => 'cliente',
                'numero' => $i,
                'nif' => $nif,
                'nif_hash' => hash('sha256', $nif),
                'nome' => "Cliente Exemplo $i",
                'morada' => "Rua do Cliente $i",
                'codigo_postal' => '4000-001',
                'localidade' => 'Porto',
                'pais_id' => $portugal->id,
                'telefone' => '222111333',
                'telemovel' => '912345678',
                'website' => "https://cliente$i.com",
                'email' => "cliente$i@exemplo.com",
                'consentimento_rgpd' => $i % 2 === 0 ? 'sim' : 'nao',
                'observacoes' => 'Cliente habitual',
                'estado' => $i % 2 === 0 ? 'ativo' : 'inativo',
            ]);

            // Criar 2 contactos por cliente
            for ($j = 1; $j <= 2; $j++) {
                Contacto::create([
                    'numero' => ($i - 1) * 2 + $j,
                    'entidade_id' => $cliente->id,
                    'primeiro_nome' => "Contacto$j",
                    'apelido' => "Cliente$i",
                    'funcao' => 'Gestor',
                    'telefone' => '222111999',
                    'telemovel' => '919999888',
                    'email' => "contacto$j.cliente$i@exemplo.com",
                    'consentimento_rgpd' => $j % 2 === 0 ? 'nao' : 'sim',
                    'observacoes' => 'Pessoa de contacto',
                    'estado' => 'ativo',
                ]);
            }
        }
        
        $fornecedor = null;
        foreach (range(1, 20) as $i) {
            $nif = 'ES' . str_pad($i + 100, 9, '9', STR_PAD_LEFT);
            $fornecedor = Entidade::create([
                'tipo' => 'fornecedor',
                'numero' => $i + 100,
                'nif' => $nif,
                'nif_hash' => hash('sha256', $nif),
                'nome' => "Fornecedor Exemplo $i",
                'morada' => "Rua do Fornecedor $i",
                'codigo_postal' => '5000-002',
                'localidade' => 'Braga',
                'pais_id' => $espanha->id,
                'telefone' => '223334455',
                'telemovel' => '913456789',
                'website' => "https://fornecedor$i.com",
                'email' => "fornecedor$i@exemplo.com",
                'consentimento_rgpd' => $i % 2 === 0 ? 'nao' : 'sim',
                'observacoes' => 'Fornecedor externo',
                'estado' => $i % 3 === 0 ? 'inativo' : 'ativo',
            ]);

            // Criar 2 contactos por fornecedor
            for ($j = 1; $j <= 2; $j++) {
                Contacto::create([
                    'numero' => 40 + ($i - 1) * 2 + $j,
                    'entidade_id' => $fornecedor->id,
                    'primeiro_nome' => "Contacto$j",
                    'apelido' => "Fornecedor$i",
                    'funcao' => 'Responsável',
                    'telefone' => '223334455',
                    'telemovel' => '913456789',
                    'email' => "contacto$j.fornecedor$i@exemplo.com",
                    'consentimento_rgpd' => $j % 2 === 0 ? 'sim' : 'nao',
                    'observacoes' => 'Pessoa de contacto',
                    'estado' => 'ativo',
                ]);
            }
        }

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
