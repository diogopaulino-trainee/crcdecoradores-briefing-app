<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Encomenda {{ $encomenda->numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .logo { height: 50px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border: 1px solid #ccc; }
        .no-border { border: none !important; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-2 { margin-top: 1rem; }
        .gray { background-color: #f0f0f0; }
        .highlight { color: #CDAA62; }
        .title { font-size: 20px; font-weight: bold; text-align: right; text-transform: uppercase; }
    </style>
</head>
<body>

    <table class="no-border">
        <tr class="no-border">
            <td class="no-border">
                <img
                    src="{{ $empresa && $empresa->logotipo && file_exists(storage_path('app/private/' . $empresa->logotipo))
                        ? storage_path('app/private/' . $empresa->logotipo)
                        : public_path('logos/logo_crc.png') }}"
                    class="logo"
                    alt="Logo da empresa"
                />
            </td>
            <td class="no-border title">
                {{ strtoupper($encomenda->tipo) === 'FORNECEDOR' ? 'ENCOMENDA A FORNECEDOR' : 'ENCOMENDA A CLIENTE' }}<br>
                <span class="highlight">{{ $encomenda->numero }}</span>/{{ \Carbon\Carbon::parse($encomenda->data_da_proposta)->format('Y') }}<br>
                @if ($encomenda->estado === 'Rascunho')
                    <span style="font-size: 12px; color: red;">(Rascunho - não finalizada)</span>
                @endif
            </td>
        </tr>
    </table>

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
                @if ($encomenda->estado === 'Fechado')
                    Data: <strong>{{ \Carbon\Carbon::parse($encomenda->data_da_proposta)->format('d/m/Y') }}</strong><br>
                    Validade: <strong>{{ \Carbon\Carbon::parse($encomenda->validade)->format('d/m/Y') }}</strong>
                @else
                    <em class="highlight">Encomenda em rascunho</em>
                @endif
            </td>
        </tr>
    </table>

    <table class="mt-2">
        <tr>
            <td style="width: 50%;">
                <strong>{{ $encomenda->cliente->nome }}</strong><br>
                {{ $encomenda->cliente->morada }}<br>
                {{ $encomenda->cliente->codigo_postal }} {{ $encomenda->cliente->localidade }}
            </td>
            <td>
                <table>
                    <tr class="gray">
                        <th>Cliente Nº</th>
                        <th>Contribuinte</th>
                    </tr>
                    <tr>
                        <td>{{ $encomenda->cliente->numero }}</td>
                        <td>{{ $encomenda->cliente->nif }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <h3 class="mt-2">ARTIGOS</h3>

    <table>
        <tr class="gray">
            <th>Ref</th>
            <th>Descrição</th>
            <th>Qtd</th>
            <th>IVA</th>
            <th>Preço</th>
        </tr>
        @php $subtotal = 0; $totalIva = 0; @endphp
        @foreach ($encomenda->linhas as $linha)
            @php
                $precoLinha = $linha->quantidade * $linha->preco_unitario;
                $iva = $linha->artigo->iva->percentagem ?? 0;
                $ivaLinha = $precoLinha * ($iva / 100);
                $subtotal += $precoLinha;
                $totalIva += $ivaLinha;
            @endphp
            <tr>
                <td>{{ $linha->artigo->referencia }}</td>
                <td>{{ $linha->artigo->nome }}<br><small>{{ $linha->artigo->descricao }}</small></td>
                <td class="text-center">{{ $linha->quantidade }}</td>
                <td class="text-center">{{ $iva }}%</td>
                <td class="text-right">€ {{ number_format($precoLinha, 2, ',', '.') }}</td>
            </tr>
        @endforeach
        @php $totalComIva = $subtotal + $totalIva; @endphp
    </table>

    <table class="mt-2">
        <tr>
            <td style="width: 50%;">
                <strong>Observações:</strong><br><br>
                Esta encomenda é válida por 30 dias.
            </td>
            <td>
                <table>
                    <tr><td>Subtotal</td><td class="text-right">€ {{ number_format($subtotal, 2, ',', '.') }}</td></tr>
                    <tr><td>IVA</td><td class="text-right">€ {{ number_format($totalIva, 2, ',', '.') }}</td></tr>
                    <tr class="gray">
                        <td><strong>Total com IVA</strong></td>
                        <td class="text-right"><strong>€ {{ number_format($totalComIva, 2, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
