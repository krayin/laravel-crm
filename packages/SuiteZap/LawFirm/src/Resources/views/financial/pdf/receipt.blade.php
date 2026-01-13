<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Recibo Financeiro</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
        }

        /* Cabeçalho com Logo à Esquerda (Absoluto) e Texto Centralizado */
        .header-container {
            position: relative;
            /* Pai relativo */
            height: 100px;
            /* Altura fixa para caber a logo */
            border-bottom: 2px solid #ddd;
            margin-bottom: 30px;
        }

        .logo-img {
            position: absolute;
            left: 0;
            top: 10px;
            max-height: 80px;
            max-width: 150px;
        }

        .company-info {
            text-align: center;
            width: 100%;
            padding-top: 25px;
            /* Ajuste para centralizar verticalmente com a logo */
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
        }

        .content {
            padding: 20px 0;
        }

        .box {
            border: 1px solid #ccc;
            padding: 15px;
            background: #f9f9f9;
            margin: 20px 0;
        }

        .value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Rodapé Limpo (Sem Emojis) */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 10px;
            color: #555;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>

<body>

    <div class="header-container">
        @if(!empty($realLogoPath) && file_exists($realLogoPath))
            <img src="{{ $realLogoPath }}" class="logo-img">
        @endif

        <div class="company-info">
            <span class="company-name">{{ $companyName }}</span>
            <!-- Se tiver CNPJ ou outra info, pode por aqui -->
        </div>
    </div>

    <!-- CONTEÚDO DO RECIBO -->
    <div class="content">
        <h2 style="text-align: center;">RECIBO DE PAGAMENTO</h2>

        <div class="box" style="text-align: center;">
            <div>Valor Recebido</div>
            <div class="value">R$ {{ number_format($transaction->valor, 2, ',', '.') }}</div>
        </div>

        <p>
            Recebemos de
            <strong>{{ $transaction->person->name ?? $transaction->processo->person->name ?? 'Cliente' }}</strong>
            a importância supra mencionada, referente a
            <strong>{{ $transaction->nome ?? 'Honorários Advocatícios' }}</strong>.
        </p>

        <p>
            Para maior clareza firmamos o presente.
        </p>

        <br><br><br>
        <div style="text-align: center;">
            __________________________________________________<br>
            Assinatura do Responsável<br>
            {{ date('d/m/Y') }}
        </div>
    </div>

    <div class="footer">
        <!-- Formato: WhatsApp / Endereço -->
        <div class="contact-info">
            @if($whatsapp)
                <strong>Contato:</strong> {{ $whatsapp }}
            @endif

            @if($whatsapp && $address)
                &nbsp; | &nbsp; <!-- Separador Visual -->
            @endif

            @if($address)
                <strong>Endereço:</strong> {!! nl2br(e($address)) !!}
            @endif

            @if($website)
                <br>Site: {{ $website }}
            @endif
        </div>
    </div>

</body>

</html>