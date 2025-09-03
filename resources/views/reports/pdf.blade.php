@php
    use Illuminate\Support\Str;

    $fmtData = fn($dt) => optional($dt)->timezone(config('app.timezone'))->format('d/m/Y');
    $fmtHora = fn($dt) => optional($dt)->timezone(config('app.timezone'))->format('H:i');

    $dept =
        // Autor
        ($report->author?->department?->sigla
        ?? $report->author?->department?->name
        ?? $report->author?->department?->nome)
        // Relator
        ?? ($report->relator?->department?->sigla
        ??  $report->relator?->department?->name
        ??  $report->relator?->department?->nome)
        // Report (opcional)
        ?? ($report->department?->sigla
        ??  $report->department?->name
        ??  $report->department?->nome)
        // Fallback
        ?? 'DEPARTAMENTO';
@endphp


    <!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório Diário - #{{ $report->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
            background: #fff;
        }

        .header {
            text-align: center;
            padding: 20px 10px;
            border-bottom: 3px solid #000;
            margin-bottom: 20px;
        }
        .logo {
            display: block;
            margin: 0 auto 12px auto;
            max-height: 80px;
        }

        .header img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .section {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 16px;
        }

        h3 {
            background: #f2f2f2;
            padding: 6px 10px;
            margin: -12px -14px 12px -14px;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
        }

        .grid {
            display: flex;
            gap: 20px;
        }

        .grid > div {
            flex: 1;
        }

        .field {
            margin-bottom: 8px;
        }

        .field strong {
            display: inline-block;
            width: 130px;
        }

        ul {
            padding-left: 20px;
            margin: 0;
        }

        li {
            margin-bottom: 6px;
        }

        .muted {
            font-size: 11px;
            color: #666;
            text-align: right;
            margin-top: 40px;
        }

        .odometro {
            background: #f9f9f9;
            padding: 6px 10px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }
    </style>

</head>
<body>

<div class="header">
    <img src="{{ public_path('img/semtran.png') }}" alt="SEMTRAN" class="logo">
    <h1 style="margin: 8px 0 2px 0;">PREFEITURA DE PORTO VELHO</h1>
    <div>SECRETARIA MUNICIPAL DE SEGURANÇA, TRÂNSITO E MOBILIDADE</div>
    <h2 style="margin-top: 6px; font-size: 14px; text-transform: uppercase;">
        RELATÓRIO DIÁRIO - {{ Str::upper($dept) }}
    </h2>
</div>


{{-- 1. Informações Gerais (mostra se houver datas/turno/tipo) --}}
@if($report->start_at || $report->end_at || $report->service_type || $report->shift)
    <div class="section">
        <h3>1. Informações Gerais</h3>
        <div class="grid-2">
            <div class="col">
                @if($report->start_at)
                    <div class="mb-6"><strong>Data:</strong>
                        {{ $fmtData($report->start_at) }}
                        @if($report->end_at && $fmtData($report->end_at) !== $fmtData($report->start_at))
                            — {{ $fmtData($report->end_at) }}
                        @endif
                    </div>
                @endif

                @if($report->start_at || $report->end_at)
                    <div class="mb-6"><strong>Horário:</strong>
                        @if($report->start_at) {{ $fmtHora($report->start_at) }} @endif
                        @if($report->end_at) - {{ $fmtHora($report->end_at) }} @endif
                    </div>
                @endif
            </div>
            <div class="col">
                @if($report->service_type)
                    <div class="mb-6"><strong>Tipo de Serviço:</strong> {{ Str::title($report->service_type) }}</div>
                @endif
                @if($report->shift)
                    <div class="mb-6"><strong>Turno:</strong> {{ Str::title($report->shift) }}</div>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- 2. Equipe --}}
@if($report->team && $report->team->isNotEmpty())
    <div class="section">
        <h3>2. Equipe</h3>
        <div>
            {{ $report->team->pluck('name')->join(', ') }}
        </div>
    </div>
@endif

{{-- 3. Descrição das Atividades --}}
@if(($report->atividades && $report->atividades->isNotEmpty()) || filled($report->descricao_manual))
    <div class="section">
        <h3>3. Descrição das Atividades</h3>

        @if($report->atividades && $report->atividades->isNotEmpty())
            <h4>3.1 Atividades Estruturadas</h4>
            <ol style="margin: 4px 0 0 18px;">
                @foreach($report->atividades->sortBy('ordem') as $i => $a)
                    <li style="margin-bottom:6px;">
                        <div><strong>Tipo:</strong> {{ $a->tipo->nome ?? '-' }}</div>
                        @if($a->situacao) <div><strong>Situação:</strong> {{ $a->situacao->nome }}</div> @endif
                        @if($a->medida)   <div><strong>Medida Adotada:</strong> {{ $a->medida->nome }}</div> @endif

                    </li>
                @endforeach
            </ol>
        @endif

        @if(filled($report->descricao_manual))
            <h4 style="margin-top:8px;">3.2 Descrição Manual</h4>
            <div>{!! nl2br(e($report->descricao_manual)) !!}</div>
        @endif
    </div>
@endif

{{-- 4. Equipamentos --}}
@if($report->equipamentos && $report->equipamentos->isNotEmpty())
    <div class="section">
        <h3>4. Equipamentos</h3>
        <ul>
            @foreach($report->equipamentos->sortBy('ordem') as $e)
                <li>
                    <strong>{{ Str::upper($e->tipo) }}</strong>
                    @if(filled($e->outro_texto) && $e->tipo === 'outros')
                        : {{ $e->outro_texto }}
                    @endif
                    @if($e->quantidade) : {{ $e->quantidade }} @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 5. Veículos e Condutores (opcional — só se tiver condutores) --}}
@if($report->condutores && $report->condutores->isNotEmpty())
    <div class="section">
        <h3>5. Veículos e Condutores</h3>
        <ul>
            @foreach($report->condutores->sortBy('ordem') as $c)
                <li>
                    <div><strong>Veículo:</strong> {{ $c->veiculo?->descricao ?? '-' }}</div>
                    <div><strong>Motorista:</strong> {{ $c->motorista?->name ?? '-' }}
                        @php
                            $mat = $c->motorista?->registration ?? $c->motorista?->matricula;
                        @endphp
                        @if($mat) (Mat. {{ $mat }}) @endif
                    </div>
                    @if(!is_null($c->odometro_inicial) || !is_null($c->odometro_final))
                        <div><strong>Odômetro:</strong>
                            {{ $c->odometro_inicial ?? 0 }} → {{ $c->odometro_final ?? 0 }}
                            @php
                                $dist = max(0, (int)($c->odometro_final) - (int)($c->odometro_inicial));
                            @endphp
                            ({{ $dist }} km)
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 6. Observações Gerais (RichEditor) --}}
@if(filled($report->observacoes))
    <div class="section">
        <h3>6. Observações Gerais</h3>
        <div>{!! $report->observacoes !!}</div>
    </div>
@endif

{{-- 7. Agente Relator --}}
@if($report->relator)
    <div class="section">
        <h3>7. Agente Relator</h3>
        <div><strong>Nome:</strong> {{ $report->relator->name }}</div>
        @php
            $mat = $report->relator->registration ?? $report->relator->matricula;
        @endphp
        @if($mat)
            <div><strong>Matrícula:</strong> {{ $mat }}</div>
        @endif
    </div>
@endif

<hr class="hr">
<div class="muted">
    Gerado em: {{ now()->format('d/m/Y \à\s H:i:s') }}
    @if($report->author)
        por {{ $report->author->name }}
        @php $amat = $report->author->registration ?? $report->author->matricula; @endphp
        @if($amat) (Mat. {{ $amat }}) @endif
    @endif
</div>
</body>
</html>
