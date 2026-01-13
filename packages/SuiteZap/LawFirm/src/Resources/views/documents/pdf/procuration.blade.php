<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Procuração</title>
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

    <h1>Procuração Ad Judicia</h1>

    <p>
        <span class="label">OUTORGANTE:</span>
        <strong>{{ $client['name'] }}</strong>,
        @if(!empty($client['nationality'])) {{ $client['nationality'] }}, @endif
        @if(!empty($client['civil_status'])) {{ $client['civil_status'] }}, @endif
        @if(!empty($client['profession'])) {{ $client['profession'] }}, @endif
        inscrito(a) no CPF sob nº {{ $client['cpf'] ?? '________________' }},
        @if($client['rg']) RG nº {{ $client['rg'] }}, @endif
        residente e domiciliado(a) em
        @if($client['address'])
            {{ $client['address'] }}.
        @else
            ______________________________________________________________________________________________________.
        @endif
    </p>

    <p>
        <span class="label">OUTORGADO(S):</span><br>
        @if($lawyerSpecificName)
            <strong>{{ $lawyerSpecificName }}</strong>, advogado(a), inscrito(a) na OAB sob nº {{ $lawyerSpecificOAB }};<br>
            ambos integrantes da sociedade de advocacia
        @endif
        <strong>{{ $firmName }}</strong>, inscrita na OAB sob nº {{ $firmOAB }},
        com escritório profissional situado na {{ $firmAddress }}.
    </p>

    <p>
        <span class="label">PODERES:</span>
        Pelo presente instrumento particular de mandato, o(a) OUTORGANTE nomeia e constitui o(a) OUTORGADO(A) seu(sua)
        bastante procurador(a), conferindo-lhe amplos poderes para o foro em geral, com a cláusula <em>ad judicia et
            extra</em>, em qualquer Juízo, Instância ou Tribunal, podendo propor contra quem de direito, as ações
        competentes e defendê-lo(a) nas contrárias, seguindo umas e outras, até final decisão, usando os recursos legais
        e acompanhando-os, conferindo-lhe ainda, poderes especiais para confessar, desistir, transigir, firmar
        compromissos ou acordos, receber e dar quitação, agindo em conjunto ou separadamente, podendo ainda
        substabelecer esta a outrem, com ou sem reservas de iguais poderes, para o fiel cumprimento do presente mandato.
    </p>

    <p>
        <span class="label">FINALIDADE ESPECÍFICA (SEM PREJUÍZO DOS PODERES GERAIS):</span><br>
        Propor, acompanhar e praticar todos os atos necessários à defesa dos interesses do(a) OUTORGANTE em
        <strong>Ação {{ $process->area_direito ?? 'Judicial' }}</strong>
        @if(!empty($process->numero_cnj)) (Processo nº {{ $process->numero_cnj }}) @endif,
        em face de quem de direito, incluindo fase de conhecimento, liquidação, cumprimento de sentença e execução, até
        final satisfação do crédito.
    </p>

    <p style="text-align: right; margin-top: 1.5cm;">
        {{ $city }}, {{ $dateExtenso }}.
    </p>

    <div class="signature-block">
        <div class="line"></div>
        <strong>{{ $client['name'] }}</strong><br>
        Outorgante
    </div>

    <div class="footer">
        {{ $firmName }} @if($firmOAB) | {{ $firmOAB }} @endif <br>
        {{ core()->getConfigData('lawfirm.settings.general.website') ?? '' }}
    </div>

</body>

</html>