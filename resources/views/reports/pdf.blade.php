@php
    use Illuminate\Support\Str;

    // Helpers rápidos
    $fmtData = fn($dt) => optional($dt)->timezone(config('app.timezone'))->format('d/m/Y');
    $fmtHora = fn($dt) => optional($dt)->timezone(config('app.timezone'))->format('H:i');
@endphp
    <!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório Diário - #{{ $report->id }}</title>
    <style>
        body{ font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        h1,h2,h3{ margin: 0 0 6px; }
        .muted{ color:#555; }
        .section{ margin: 14px 0; }
        .head{ text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
        .grid-2{ display: table; width: 100%; table-layout: fixed; }
        .col{ display: table-cell; vertical-align: top; }
        .mb-6{ margin-bottom: 6px; }
        ul{ margin: 4px 0 0 16px; padding:0; }
        .tag{ display:inline-block; padding:2px 6px; border:1px solid #333; border-radius:4px; font-size:11px; margin-right:6px; }
        .hr{ border:0; border-top:1px solid #ddd; margin:10px 0; }
    </style>
</head>
<body>

<div class="head">
    <div><strong>PREFEITURA DE PORTO VELHO</strong></div>
    <div>SECRETARIA MUNICIPAL DE SEGURANÇA, TRÂNSITO E MOBILIDADE</div>
    <div><strong>RELATÓRIO DIÁRIO - DICS</strong></div>
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
