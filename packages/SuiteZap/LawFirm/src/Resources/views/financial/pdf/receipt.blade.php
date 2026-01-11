<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recibo de Pagamento</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a365d;
        }

        .header h2 {
            font-size: 18px;
            font-weight: normal;
            margin-top: 10px;
            color: #4a5568;
        }

        .receipt-number {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 20px;
        }

        .amount-box {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .amount-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
        }

        .amount-value {
            font-size: 28px;
            font-weight: bold;
            color: #2f855a;
            margin-top: 5px;
        }

        .details {
            margin-bottom: 40px;
        }

        .details p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .details strong {
            color: #1a365d;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
        }

        .date-location {
            margin-bottom: 50px;
            font-size: 13px;
        }

        .signature {
            border-top: 1px solid #333;
            width: 300px;
            margin: 0 auto;
            padding-top: 10px;
            font-size: 12px;
        }

        .legal-notice {
            margin-top: 40px;
            font-size: 10px;
            color: #999;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ADVOCACIA</h1>
            <h2>Recibo de Pagamento</h2>
        </div>

        <div class="receipt-number">
            Recibo Nº {{ str_pad($recibo->id, 6, '0', STR_PAD_LEFT) }}
        </div>

        <div class="amount-box">
            <div class="amount-label">Valor Recebido</div>
            <div class="amount-value">R$ {{ number_format($recibo->valor, 2, ',', '.') }}</div>
        </div>

        <div class="details">
            <p>
                Recebemos de <strong>{{ $pagador }}</strong> a importância de
                <strong>R$ {{ number_format($recibo->valor, 2, ',', '.') }}</strong>,
                referente a <strong>{{ $recibo->nome ?? 'Serviços jurídicos' }}</strong>
                do processo <strong>{{ $processoTitulo }}</strong>.
            </p>

            @if($recibo->payment_method)
                <p>
                    <strong>Forma de Pagamento:</strong> {{ ucfirst($recibo->payment_method) }}
                </p>
            @endif
        </div>

        <div class="footer">
            <div class="date-location">
                @if($dataExtenso)
                    {{ $dataExtenso }}
                @else
                    {{ now()->format('d/m/Y') }}
                @endif
            </div>

            <div class="signature">
                Assinatura do Responsável
            </div>
        </div>

        <div class="legal-notice">
            Este recibo é válido como comprovante de pagamento.
            Documento gerado eletronicamente.
        </div>
    </div>
</body>

</html>