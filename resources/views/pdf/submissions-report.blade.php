<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Cadastros</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, sans-serif;
            padding: 30px;
            color: #2d3748;
            font-size: 9px;
        }

        .header {
            margin-bottom: 20px;
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
            width: 200px;
        }

        .header-left img {
            max-height: 180px;
            max-width: 190px;
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
            font-size: 9px;
            color: #718096;
            letter-spacing: 0.3px;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 200px;
            font-size: 10px;
            color: #4a5568;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            font-family: Helvetica, sans-serif;
        }

        thead th {
            background-color: #e2e8f0;
            color: #2d3748;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 6px;
            text-align: left;
            font-size: 8px;
            border: 1px solid #cbd5e0;
        }

        tbody td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            word-wrap: break-word;
        }

        tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .text-center {
            text-align: center;
        }

        .col-id {
            width: 35px;
            text-align: center;
        }

        .col-date {
            width: 75px;
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e0;
            font-size: 8px;
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
                <h1>{{ $tenant->name ?? 'Relatório de Cadastros' }}</h1>
                <div class="subtitle">{{ $submissions->count() }} {{ $submissions->count() === 1 ? 'cadastro' : 'cadastros' }} &mdash; Gerado em {{ now()->setTimezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}</div>
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

    <h2 style="text-align: center; font-size: 13px; color: #2d3748; margin-bottom: 15px; letter-spacing: 0.5px;">Relatório de Cadastros</h2>

    @if($submissions->isEmpty())
        <div style="text-align: center; padding: 40px 20px; color: #718096; font-size: 12px;">
            Não existem lançamentos registrados.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    @foreach($questions as $question)
                        <th>{{ $question->title }}</th>
                    @endforeach
                    <th class="col-date">Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    <tr>
                        <td class="col-id">{{ $submission->id }}</td>
                        @foreach($questions as $question)
                            <td>
                                @php
                                    $answer = $submission->answers->firstWhere('question_id', $question->id);
                                @endphp
                                {{ $answer?->answer ?? '-' }}
                            </td>
                        @endforeach
                        <td class="col-date">{{ \Carbon\Carbon::parse($submission->created_at)->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Gerado por {{ auth()->user()->name }} em {{ now()->setTimezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}
    </div>
</body>
</html>
