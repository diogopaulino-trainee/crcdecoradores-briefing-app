<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Proposta {{ $proposta->numero }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .logo {
            height: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 6px 8px;
            border: 1px solid #ccc;
        }

        .no-border {
            border: none !important;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: right;
        }

        .gray {
            background-color: #f0f0f0;
        }

        .highlight {
            color: #CDAA62;
        }
    </style>
</head>
<body>

    <!-- Topo -->
    <table class="no-border">
        <tr class="no-border">
            <td class="no-border">
                <img
                    src="{{ $empresa && $empresa->logotipo && file_exists(storage_path('app/private/' . $empresa->logotipo))
                        ? storage_path('app/private/' . $empresa->logotipo)
                        : public_path('logos/logo_crc.png') }}"
                    class="logo"
                    alt="Logotipo da empresa"
                />
                        </td>
            <td class="no-border title">
                ORÇAMENTO<br>
                <span class="highlight">{{ $proposta->numero }}</span>/{{ \Carbon\Carbon::parse($proposta->data_da_proposta)->format('Y') }}
            </td>
        </tr>
    </table>

    <!-- Bloco de Identificação + Edição -->
    <table class="mt-2">
        <tr>
            <td style="width: 50%; border: none;">
                @if ($empresa)
                    <strong>{{ $empresa->nome }}</strong><br>
                    {{ $empresa->morada }}<br>
                    {{ $empresa->codigo_postal }} {{ $empresa->localidade }}<br>
                    NIF: {{ $empresa->numero_contribuinte }}
                @else
                    <strong>Empresa</strong><br>
                    Dados não disponíveis
                @endif
            </td>
            <td style="width: 50%; border: none; text-align: right;">
                Edição: <strong>1</strong><br>
                {{ \Carbon\Carbon::parse($proposta->data_da_proposta)->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <!-- Cliente + Contribuinte lado a lado -->
    <table class="mt-2">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table style="width: 100%; border: none;">
                    <tr><td><strong>{{ $proposta->cliente->nome }}</strong></td></tr>
                    <tr><td>{{ $proposta->cliente->morada }}</td></tr>
                    <tr><td>{{ $proposta->cliente->codigo_postal }} {{ $proposta->cliente->localidade }}</td></tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table style="width: 100%;">
                    <tr class="gray">
                        <th>Cliente Nº</th>
                        <th>Contribuinte</th>
                    </tr>
                    <tr>
                        <td>{{ $proposta->cliente->numero }}</td>
                        <td>{{ $proposta->cliente->nif }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Pedido -->
    <h3 class="mt-2">PEDIDO #</h3>

    <table>
        <tr class="gray">
            <th>Ref</th>
            <th>Descrição</th>
            <th>Qtd</th>
            <th>IVA</th>
            <th>Preço</th>
        </tr>
        @php $subtotal = 0; $totalIva = 0; @endphp
        @foreach ($proposta->linhas as $linha)
            @php
                $precoLinha = $linha->quantidade * $linha->preco_unitario;
                $ivaLinha = $precoLinha * ($linha->artigo->iva->percentagem / 100);
                $subtotal += $precoLinha;
                $totalIva += $ivaLinha;
            @endphp
            <tr>
                <td>{{ $linha->artigo->referencia }}</td>
                <td>
                    {{ $linha->artigo->nome }}<br>
                    <small>{{ $linha->artigo->descricao }}</small>
                </td>
                <td class="text-center">{{ number_format($linha->quantidade, 2, ',', '.') }} Un</td>
                <td class="text-center">{{ $linha->artigo->iva->percentagem }}%</td>
                <td class="text-right">€ {{ number_format($precoLinha, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        @php $totalComIva = $subtotal + $totalIva; @endphp
    </table>

    <!-- Termos e Totais -->
    <table class="mt-2">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <strong>Termos e Condições</strong><br><br>
                Prazo Entrega: 30 dias<br>
                Condições de pagamento:<br>
                - Adjudicação: 50%<br>
                - Conclusão: 50%<br>
                Válido até: {{ \Carbon\Carbon::parse($proposta->validade)->format('d/m/Y') }}<br><br>
                *Valor sem IVA incluído<br><br>
                <strong>IBANs:</strong><br>
                PT50 0033 0000 0011 7871 8795 05<br>
                PT50 0269 0660 0020 0963 0111 32
            </td>
            <td style="width: 50%;">
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">€ {{ number_format($subtotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Desconto</td>
                        <td class="text-right">€ 0,00</td>
                    </tr>
                    <tr>
                        <td>Total sem IVA</td>
                        <td class="text-right">€ {{ number_format($subtotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>IVA</td>
                        <td class="text-right">€ {{ number_format($totalIva, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="gray">
                        <td><strong>Total com IVA</strong></td>
                        <td class="text-right"><strong>€ {{ number_format($totalComIva, 2, ',', '.') }}</strong></td>
                    </tr>
                </table>

                <p class="mt-2 text-center">Este documento não serve de fatura</p>
            </td>
        </tr>
    </table>

    <!-- MB MOCK -->
    <table class="mt-2">
        <tr class="gray">
            <th colspan="3">MULTIBANCO</th>
        </tr>
        <tr>
            <td>Entidade</td>
            <td>Referência</td>
            <td>Valor (50%)</td>
        </tr>
        <tr>
            <td>11473</td>
            <td>969 006 950</td>
            <td>€ {{ number_format($totalComIva / 2, 2, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Rodapé -->
    <div class="mt-2" style="font-size: 10px; text-align: center;">
        Rua António Saldanha 58 1500-048 Lisboa • T. 217 770 850 • M. 911 888 899<br>
        geral@crcdecoradores.com • www.crcdecoradores.com
    </div>

</body>
</html>
