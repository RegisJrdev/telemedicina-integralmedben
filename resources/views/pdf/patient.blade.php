<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paciente #{{ $patient->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, sans-serif;
            padding: 40px;
            color: #2d3748;
            line-height: 1.6;
        }

        .header {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #cbd5e0;
        }

        .header-table {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 170px;
        }

        .header-left img {
            max-height: 150px;
            max-width: 160px;
        }

        .header-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .header-center h1 {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .header-center .subtitle {
            font-size: 10px;
            color: #718096;
            letter-spacing: 0.3px;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 200px;
            font-size: 11px;
            color: #4a5568;
            line-height: 1.5;
        }

        .content-section {
            margin-top: 30px;
        }

        .section-title {
            font-family: Helvetica, sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #000;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .answer-item {
            margin-bottom: 16px;
            page-break-inside: avoid;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .answer-item .question {
            font-weight: 600;
            font-size: 11px;
            color: #2d3748;
            background-color: #e2e8f0;
            padding: 8px 12px;
        }

        .answer-item .answer {
            font-size: 11px;
            color: #2d3748;
            background-color: #ffffff;
            padding: 8px 12px;
        }

        .footer {
            position: fixed;
            bottom: 30px;
            left: 40px;
            right: 40px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e0;
            font-size: 9px;
            color: #718096;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-table">
            <div class="header-left">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </div>
            <div class="header-center">
                <h1>{{ $tenant->name ?? 'Ficha do Paciente' }}</h1>
                <div class="subtitle">Paciente #{{ $patient->id }} &mdash; {{ \Carbon\Carbon::parse($patient->created_at)->setTimezone('America/Sao_Paulo')->format('d/m/Y') }}</div>
            </div>
            <div class="header-right">
                @if($tenant->logradouro || $tenant->cidade)
                    @if($tenant->logradouro){{ $tenant->logradouro }}@endif
                    @if($tenant->numero), {{ $tenant->numero }}@endif
                    @if($tenant->complemento) - {{ $tenant->complemento }}@endif
                    <br>
                    @if($tenant->bairro){{ $tenant->bairro }}@endif
                    @if($tenant->cidade) &mdash; {{ $tenant->cidade }}@endif
                    @if($tenant->estado)/{{ $tenant->estado }}@endif
                    <br>
                    @if($tenant->cep)CEP: {{ $tenant->cep }}@endif
                @endif
            </div>
        </div>
    </div>

    <div class="content-section">
        <h2 class="section-title">Ficha de Cadastro</h2>

        @foreach($patient->answers as $answer)
            <div class="answer-item">
                <div class="question">{{ $answer->question->title }}</div>
                <div class="answer">{{ $answer->answer ?? 'Não respondido' }}</div>
            </div>
        @endforeach
    </div>

    <div class="footer">
        Gerado por {{ auth()->user()->name }} em {{ now()->setTimezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}
    </div>
</body>
</html>
