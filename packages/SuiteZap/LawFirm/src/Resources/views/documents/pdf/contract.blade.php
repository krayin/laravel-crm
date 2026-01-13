<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Contrato de Honorários</title>
    <style>
        @page {
            margin: 1.5cm 2.5cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }

        h1 {
            text-align: center;
            text-transform: uppercase;
            font-size: 14pt;
            margin-bottom: 1.5cm;
            font-weight: bold;
        }

        p {
            text-align: justify;
            text-indent: 0;
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-block {
            margin-top: 2.5cm;
            text-align: center;
            page-break-inside: avoid;
        }

        .line {
            border-top: 1px solid #000;
            width: 60%;
            margin: 0 auto 5px auto;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    <h1>Contrato de Honorários Advocatícios</h1>

    <p>
        <span class="label">CONTRATANTE:</span>
        <strong>{{ $client['name'] }}</strong>,
        {{ $client['doc_type'] }} nº {{ $client['doc'] }},
        residente e domiciliado(a) em
        {{ $client['address'] ?? '______________________________________________________' }}.
    </p>

    <p>
        <span class="label">CONTRATADO(S):</span><br>
        @if($lawyerSpecificName)
            <strong>{{ $lawyerSpecificName }}</strong>, advogado(a), inscrito(a) na OAB sob nº {{ $lawyerSpecificOAB }},
            integrante da sociedade de advocacia
        @endif
        <strong>{{ $firmName }}</strong>, inscrita na OAB sob nº {{ $firmOAB }},
        com escritório profissional situado na {{ $firmAddress }}.
    </p>

    <p>
        <span class="label">OBJETO:</span>
        O presente contrato tem como objeto a prestação de serviços jurídicos para defesa dos interesses do CONTRATANTE
        na
        <strong>Ação {{ $process->area_direito ?? 'Judicial' }}</strong>
        (Ref: {{ $process->titulo }}).
        @if(!empty($process->numero_cnj) && strlen($process->numero_cnj) > 5)
            Processo nº <strong>{{ $process->numero_cnj }}</strong>.
        @else
            (Ação a ser distribuída/protocolada).
        @endif
    </p>

    <p>
        <span class="label">HONORÁRIOS:</span>
        Em remuneração aos serviços profissionais ora contratados, o CONTRATANTE pagará ao CONTRATADO o valor pactuado
        em proposta anexa, acrescido de honorários de sucumbência que vierem a ser arbitrados pelo Juízo, na forma da
        Lei nº 8.906/94.
    </p>

    <p>
        <span class="label">FORO:</span>
        As partes elegem o foro da Comarca de <strong>{{ $city }}</strong> para dirimir quaisquer dúvidas oriundas do
        presente contrato.
    </p>

    <p style="text-align: right; margin-top: 1.5cm;">
        {{ $city }}, {{ $dateExtenso }}.
    </p>

    <div class="signature-block">
        <div class="line"></div>
        <strong>{{ $client['name'] }}</strong><br>
        Contratante
    </div>

    <div class="footer">
        {{ $firmName }} @if($firmOAB) | {{ $firmOAB }} @endif <br>
        {{ core()->getConfigData('lawfirm.settings.general.website') ?? '' }}
    </div>

</body>

</html>