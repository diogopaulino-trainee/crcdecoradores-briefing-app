<?php

namespace Database\Seeders;

use App\Models\Artigo;
use App\Models\Contacto;
use App\Models\Empresa;
use App\Models\Encomenda;
use App\Models\Entidade;
use App\Models\FaturaFornecedor;
use App\Models\Funcao;
use App\Models\Iva;
use App\Models\LinhaEncomenda;
use App\Models\LinhaProposta;
use App\Models\OrdemTrabalho;
use App\Models\Pais;
use App\Models\Proposta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        $iva23 = Iva::where('percentagem', 23)->first();
        $iva6 = Iva::where('percentagem', 6)->first();
        $iva0 = Iva::where('percentagem', 0)->first();

        $ivas = [$iva23, $iva6, $iva0];

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
        $numeroContacto = 1;

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
                'consentimento_rgpd' => $i % 2 === 0 ? 'Sim' : 'Não',
                'observacoes' => 'Cliente habitual',
                'estado' => $i % 2 === 0 ? 'Ativo' : 'Inativo',
            ]);

            // Criar 2 contactos por cliente
            for ($j = 1; $j <= 2; $j++) {
                Contacto::create([
                    'numero' => $numeroContacto++,
                    'entidade_id' => $cliente->id,
                    'primeiro_nome' => "Contacto$j",
                    'apelido' => "Cliente$i",
                    'funcao' => 'Gestor',
                    'telefone' => '222111999',
                    'telemovel' => '919999888',
                    'email' => "contacto$j.cliente$i@exemplo.com",
                    'consentimento_rgpd' => $j % 2 === 0 ? 'Não' : 'Sim',
                    'observacoes' => 'Pessoa de contacto',
                    'estado' => 'Ativo',
                ]);
            }
        }
        
        $fornecedor = null;
        $ultimoNumero = Entidade::max('numero') ?? 0;

        foreach (range(1, 20) as $i) {
            $numero = $ultimoNumero + $i;

            $fornecedor = Entidade::create([
                'tipo' => 'fornecedor',
                'numero' => $numero,
                'nif' => 'ES' . str_pad($i + 100, 9, '9', STR_PAD_LEFT),
                'nif_hash' => hash('sha256', 'ES' . str_pad($i + 100, 9, '9', STR_PAD_LEFT)),
                'nome' => "Fornecedor Exemplo $i",
                'morada' => "Rua do Fornecedor $i",
                'codigo_postal' => '5000-002',
                'localidade' => 'Braga',
                'pais_id' => $espanha->id,
                'telefone' => '223334455',
                'telemovel' => '913456789',
                'website' => "https://fornecedor$i.com",
                'email' => "fornecedor$i@exemplo.com",
                'consentimento_rgpd' => $i % 2 === 0 ? 'Não' : 'Sim',
                'observacoes' => 'Fornecedor externo',
                'estado' => $i % 3 === 0 ? 'Inativo' : 'Ativo',
            ]);

            // Criar 2 contactos por fornecedor
            for ($j = 1; $j <= 2; $j++) {
                Contacto::create([
                    'numero' => $numeroContacto++,
                    'entidade_id' => $fornecedor->id,
                    'primeiro_nome' => "Contacto$j",
                    'apelido' => "Fornecedor$i",
                    'funcao' => 'Responsável',
                    'telefone' => '223334455',
                    'telemovel' => '913456789',
                    'email' => "contacto$j.fornecedor$i@exemplo.com",
                    'consentimento_rgpd' => $j % 2 === 0 ? 'Não' : 'Sim',
                    'observacoes' => 'Pessoa de contacto',
                    'estado' => 'Ativo',
                ]);
            }
        }

        $iva23 = Iva::where('percentagem', 23)->first();

        // Artigos
        Storage::disk('local')->deleteDirectory('artigos');
        Storage::disk('local')->makeDirectory('artigos');
        foreach (range(1, 50) as $i) {
            $ivaAleatorio = $ivas[array_rand($ivas)];
        
            Artigo::create([
                'referencia' => 'ART' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nome' => "Artigo Exemplo $i",
                'descricao' => "Descrição detalhada do artigo $i.",
                'preco' => rand(10, 200) + rand(0, 99) / 100,
                'iva_id' => $ivaAleatorio->id,
                'foto' => 'logo_crc.png',
                'observacoes' => $i % 5 === 0 ? 'Produto em destaque' : '—',
                'estado' => $i % 7 === 0 ? 'Inativo' : 'Ativo',
            ]);
        }

        // Criar 3 propostas com múltiplas linhas
        for ($p = 1; $p <= 3; $p++) {
            $dataProposta = now()->subDays(5 + $p);

            $proposta = Proposta::create([
                'numero' => 1000 + $p,
                'data_da_proposta' => $dataProposta,
                'cliente_id' => $cliente->id,
                'validade' => $dataProposta->copy()->addDays(30),
                'estado' => 'Fechado',
                'valor_total' => 0,
            ]);
        
            $total = 0;
        
            $artigos = Artigo::inRandomOrder()->take(rand(4, 10))->get();
        
            foreach ($artigos as $artigo) {
                $quantidade = rand(1, 3);
                $precoUnitario = $artigo->preco;
                $subtotal = $quantidade * $precoUnitario;
        
                LinhaProposta::create([
                    'proposta_id' => $proposta->id,
                    'artigo_id' => $artigo->id,
                    'fornecedor_id' => $fornecedor->id,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                ]);
        
                $total += $subtotal + ($subtotal * ($artigo->iva->percentagem / 100));
            }
        
            $proposta->update(['valor_total' => round($total, 2)]);
        }

        // Encomenda cliente
        $encomendaCliente = Encomenda::create([
            'tipo' => 'cliente',
            'numero' => 2001,
            'data_da_proposta' => $proposta->data_da_proposta,
            'validade' => $proposta->validade,
            'cliente_id' => $cliente->id,
            'estado' => 'Fechado',
            'valor_total' => $proposta->valor_total,
        ]);
        foreach ($proposta->linhas as $linha) {
            LinhaEncomenda::create([
                'encomenda_id' => $encomendaCliente->id,
                'artigo_id' => $linha->artigo_id,
                'fornecedor_id' => $linha->fornecedor_id,
                'quantidade' => $linha->quantidade,
                'preco_unitario' => $linha->preco_unitario,
            ]);
        }

        // Encomenda fornecedor
        $encomendaFornecedor = Encomenda::create([
            'tipo' => 'fornecedor',
            'numero' => 2002,
            'data_da_proposta' => now()->subDays(2),
            'validade' => now()->addDays(30),
            'cliente_id' => $fornecedor->id,
            'estado' => 'Fechado',
            'valor_total' => 99.90,
        ]);

        // Ordem de Trabalho
        $descricoes = [
            'Montagem de cadeiras',
            'Instalação de iluminação',
            'Reparação de móveis',
            'Entrega de material',
            'Montagem de estantes',
            'Instalação de cortinas',
            'Manutenção de escritório',
            'Decoração de sala de reuniões',
            'Reparação elétrica',
            'Reorganização de espaço',
            'Entrega urgente de cadeiras',
            'Instalação de quadros',
            'Montagem de armários',
            'Limpeza pós-obra',
            'Inspeção de equipamento',
        ];
        
        for ($i = 0; $i < 15; $i++) {
            $entidade = Entidade::inRandomOrder()->first();
        
            OrdemTrabalho::create([
                'numero' => 3001 + $i,
                'data_da_ordem' => now()->subDays(rand(0, 60)),
                'entidade_id' => $entidade->id,
                'descricao' => $descricoes[$i],
                'estado' => collect(['Pendente', 'Em Execução', 'Concluída', 'Cancelada'])->random(),
            ]);
        }

        // Fatura Fornecedor
        Storage::disk('local')->deleteDirectory('faturas/documentos');
        Storage::disk('local')->makeDirectory('faturas/documentos');
        Storage::disk('local')->deleteDirectory('faturas/comprovativos');
        Storage::disk('local')->makeDirectory('faturas/comprovativos');
        FaturaFornecedor::create([
            'numero' => 4001,
            'data_da_fatura' => now()->subDays(1),
            'data_de_vencimento' => now()->addDays(14),
            'fornecedor_id' => $fornecedor->id,
            'encomenda_fornecedor_id' => $encomendaFornecedor->id,
            'valor_total' => 99.90,
            'documento' => null,
            'comprovativo_pagamento' => null,
            'estado' => 'Paga',
        ]);
    }
}
