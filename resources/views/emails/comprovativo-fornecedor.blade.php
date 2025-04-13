<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 24px;
            background-color: #fff;
        }

        .logo {
            display: block;
            margin-top: 30px;
            height: 60px;
        }

        .assinatura {
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Estimado(a) {{ $fornecedor->nome }},</p>

        <p>Enviamos em anexo o comprovativo de pagamento da fatura <strong>{{ $fatura->numero }}</strong>.</p>

        <p>Qualquer questão, entre em contacto connosco.</p>

        <p class="assinatura">
            Cumprimentos,<br>
            <img src="{{ url('/empresa/logotipo') }}" alt="Logo CRC" class="logo">
        </p>
    </div>
</body>
</html>
