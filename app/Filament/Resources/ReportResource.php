<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Models\User;
use App\Models\Veiculo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action;


class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationLabel = 'Relatórios';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Operação';

    protected static function canFill(string $key): bool
    {
        return auth()->user()?->can("report.section.$key.fill") ?? false;
    }

    protected static function canSee(string $key): bool
    {
        $u = auth()->user();
        return $u?->can("report.section.$key.view") || $u?->can("report.section.$key.fill");
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Seção 1 — Informações Gerais
            Section::make('Informações Gerais')
                ->visible(fn () => self::canSee('informacoes_gerais'))
                ->schema([
                    Toggle::make('same_day')
                        ->label('Mesmo dia?')
                        ->default(true)
                        ->reactive()
                        ->afterStateHydrated(function (Toggle $component, ?Report $record) {
                            if ($record && $record->exists) {
                                $same = $record->same_day
                                    ?? $record->start_at?->toDateString() === $record->end_at?->toDateString();
                                $component->state($same);
                            }
                        })
                        ->disabled(fn () => ! self::canFill('informacoes_gerais')),

                    Grid::make(3)
                        ->visible(fn (Get $get) => $get('same_day') === true)
                        ->schema([
                            DatePicker::make('date_single')
                                ->label('Data')
                                ->required(fn (Get $get) => $get('same_day') === true)
                                ->afterStateHydrated(function (DatePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->toDateString());
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_start_single')
                                ->label('Hora inicial')
                                ->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === true)
                                ->afterStateHydrated(function (TimePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_end_single')
                                ->label('Hora final')
                                ->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === true)
                                ->afterStateHydrated(function (TimePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->end_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                        ]),

                    Grid::make(4)
                        ->visible(fn (Get $get) => $get('same_day') === false)
                        ->schema([
                            DatePicker::make('date_start')->label('Data inicial')
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (DatePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->toDateString());
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_start')->label('Hora inicial')->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (TimePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            DatePicker::make('date_end')->label('Data final')
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (DatePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->end_at?->toDateString());
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_end')->label('Hora final')->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (TimePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->end_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                        ]),

                    Grid::make(2)->schema([
                        Select::make('service_type')
                            ->label('Tipo de Serviço')
                            ->options([
                                'ordinario' => 'Ordinário',
                                'extraordinario' => 'Extraordinário',
                            ])
                            ->required()
                            ->dehydrated(true)
                            ->afterStateHydrated(function (Select $component, ?Report $record) {
                                if ($record) {
                                    $component->state($record->service_type);
                                }
                            })
                            ->disabled(fn () => ! self::canFill('informacoes_gerais')),

                        Select::make('shift')
                            ->label('Turno')
                            ->options([
                                'plantao' => 'Plantão',
                                'manha'   => 'Manhã',
                                'tarde'   => 'Tarde',
                                'noite'   => 'Noite',
                            ])
                            ->required()
                            ->dehydrated(true)
                            ->afterStateHydrated(function (Select $component, ?Report $record) {
                                if ($record) {
                                    $component->state($record->shift);
                                }
                            })
                            ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                    ]),
                ]),

            // Seção 2 — Equipe
            Section::make('Equipe')
                ->visible(fn () => self::canSee('equipe'))
                ->schema([
                    MultiSelect::make('team_ids')
                        ->label('Integrantes da equipe')
                        ->options(fn () => User::query()
                            ->where('department_id', auth()->user()?->department_id)
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->preload()
                        ->searchable()
                        ->disabled(fn () => ! self::canFill('equipe'))
                        ->dehydrated(false)
                        ->afterStateHydrated(function (MultiSelect $component, ?Report $record) {
                            if ($record && $record->exists) {
                                $component->state($record->team()->pluck('users.id')->all());
                            }
                        }),
                ]),

            // Seção 3 — Descrição das Atividades
            Section::make('Descrição das Atividades')
                ->visible(fn () => self::canSee('descricao_atividades'))
                ->schema([
                    \Filament\Forms\Components\Repeater::make('atividades')
                        ->label('3.1 Adicionar Atividade Estruturada')
                        ->relationship('atividades')
                        ->orderColumn('ordem')
                        ->reorderableWithButtons()
                        ->addActionLabel('Adicionar Atividade')
                        ->grid(2)
                        ->helperText('Clique em "Adicionar Atividade" para inserir itens.')
                        ->schema([
                            Select::make('tipo_atividade_id')
                                ->label('Tipo de Atividade')
                                ->options(fn () => \App\Models\TipoAtividade::orderBy('nome')->pluck('nome', 'id'))
                                ->searchable()->reactive()->required()
                                ->disabled(fn () => ! self::canFill('descricao_atividades'))
                                ->afterStateUpdated(fn ($state, callable $set) => [
                                    $set('situacao_atividade_id', null),
                                    $set('medida_atividade_id', null),
                                ]),

                            Select::make('situacao_atividade_id')
                                ->label('Situação')
                                ->options(function (Get $get) {
                                    $tipoId = $get('tipo_atividade_id');
                                    return $tipoId
                                        ? \App\Models\SituacaoAtividade::where('tipo_atividade_id', $tipoId)->orderBy('nome')->pluck('nome', 'id')
                                        : [];
                                })
                                ->searchable()->reactive()->required()
                                ->disabled(fn () => ! self::canFill('descricao_atividades'))
                                ->afterStateUpdated(fn ($state, callable $set) => $set('medida_atividade_id', null)),

                            Select::make('medida_atividade_id')
                                ->label('Medidas Adotadas')
                                ->options(function (Get $get) {
                                    $sitId = $get('situacao_atividade_id');
                                    return $sitId
                                        ? \App\Models\MedidaAtividade::where('situacao_atividade_id', $sitId)->orderBy('nome')->pluck('nome', 'id')
                                        : [];
                                })
                                ->searchable()->required()
                                ->disabled(fn () => ! self::canFill('descricao_atividades')),

                            TextInput::make('endereco')
                                ->label('Endereço')
                                ->placeholder('Insira o endereço...')
                                ->columnSpanFull()->maxLength(255)
                                ->disabled(fn () => ! self::canFill('descricao_atividades')),
                        ])
                        ->disabled(fn () => ! self::canFill('descricao_atividades')),

                    Textarea::make('descricao_manual')
                        ->label('3.2 Descrição Manual das Atividades')
                        ->placeholder('Ex.: Rondas na área central, verificação de semáforos na zona leste, apoio a eventos, etc...')
                        ->rows(6)
                        ->dehydrated(true)
                        ->afterStateHydrated(function (Textarea $component, ?Report $record) {
                            if ($record) {
                                $component->state($record->descricao_manual);
                            }
                        })
                        ->disabled(fn () => ! self::canFill('descricao_atividades')),
                ]),

            // Seção 4 — Equipamentos Utilizados
            // Seção 4 — Equipamentos Utilizados
            Section::make('Equipamentos Utilizados')
                ->visible(fn () => self::canSee('equipamentos'))
                ->schema([
                    \Filament\Forms\Components\Repeater::make('equipamentos')
                        ->label('Adicionar Equipamento')
                        ->relationship('equipamentos')
                        ->orderColumn('ordem')
                        ->reorderableWithButtons()
                        ->addActionLabel('Adicionar Equipamento')
                        ->columns(2)
                        ->schema([
                            Select::make('tipo')
                                ->label('Tipo')
                                ->options([
                                    'cones'     => 'Cones',
                                    'cavaletes' => 'Cavaletes',
                                    'barreiras' => 'Barreiras',
                                    'outros'    => 'Outros',
                                ])
                                ->required()
                                ->native(false)
                                ->dehydrated(true)
                                ->disabled(fn () => ! self::canFill('equipamentos'))
                                ->reactive(),

                            TextInput::make('outro_texto')
                                ->label('Descrição (se "Outros")')
                                ->placeholder('Descreva o equipamento...')
                                ->visible(fn (Get $get) => $get('tipo') === 'outros')
                                ->required(fn (Get $get) => $get('tipo') === 'outros')
                                ->disabled(fn () => ! self::canFill('equipamentos')),

                            TextInput::make('quantidade')
                                ->label('Quantidade')
                                ->numeric()->minValue(1)->default(1)
                                ->dehydrated(true)
                                ->disabled(fn () => ! self::canFill('equipamentos'))
                                ->columnSpanFull(),
                        ])
                        ->disabled(fn () => ! self::canFill('equipamentos')),

                    Toggle::make('informar_dados_veiculo')
                        ->label('Informar dados do veículo?')
                        ->inline(false)
                        ->reactive()
                        ->dehydrated(true)
                        ->afterStateHydrated(function (Toggle $component, ?Report $record) {
                            if ($record && $record->exists) {
                                // Marca como true se houver condutores ou se o campo informar_dados_veiculo for true
                                $component->state($record->informar_dados_veiculo || $record->condutores()->exists());
                            }
                        })
                        ->disabled(fn () => ! self::canFill('equipamentos')),
                ]),

// Seção 5 — Veículos e Condutores
            Section::make('Veículos e Condutores')
                ->visible(fn () => self::canSee('veiculos_condutores'))
                ->schema([
                    \Filament\Forms\Components\Repeater::make('condutores')
                        ->label('Motorista(s)')
                        ->relationship('condutores')
                        ->orderColumn('ordem')
                        ->reorderableWithButtons()
                        ->addActionLabel('Adicionar outro motorista')
                        ->visible(fn (Get $get, ?Report $record) => (bool) $get('informar_dados_veiculo') || ($record && $record->condutores()->exists()))
                        ->collapsed(fn (Get $get, ?Report $record) => ! ((bool) $get('informar_dados_veiculo') || ($record && $record->condutores()->exists())))
                        ->columns(2)
                        ->schema([
                            Select::make('veiculo_id')
                                ->label('Veículo / Placa')
                                ->options(function () {
                                    $deptId = auth()->user()?->department_id;
                                    if (!$deptId) return [];
                                    return \App\Models\Veiculo::query()
                                        ->visiveisParaDepartamento($deptId)
                                        ->orderBy('placa')
                                        ->get()
                                        ->pluck('descricao', 'id');
                                })
                                ->getOptionLabelUsing(fn ($value) => \App\Models\Veiculo::find($value)?->descricao ?? null)
                                ->searchable()
                                ->native(false)
                                ->required()
                                ->disabled(fn () => ! self::canFill('veiculos_condutores')),

                            Select::make('motorista_id')
                                ->label('Motorista')
                                ->options(function (Get $get, ?\App\Models\Report $record) {
                                    $ids = collect($get('../../team_ids') ?? [])
                                        ->when(!$get('../../team_ids') && $record, fn($c) => $record->team()->pluck('users.id'));
                                    return $ids->isEmpty()
                                        ? []
                                        : \App\Models\User::whereIn('id', $ids)->orderBy('name')->pluck('name', 'id');
                                })
                                ->getOptionLabelUsing(fn ($value) => \App\Models\User::find($value)?->name ?? null)
                                ->searchable()
                                ->native(false)
                                ->required()
                                ->reactive()
                                ->disabled(fn () => ! self::canFill('veiculos_condutores'))
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $u = \App\Models\User::find($state);
                                    $mat = $u?->registration ?? $u?->matricula ?? null;
                                    $set('matricula', $mat);
                                }),

                            \Filament\Forms\Components\TextInput::make('matricula')
                                ->label('Matrícula do Motorista')
                                ->readOnly(),

                            \Filament\Forms\Components\TextInput::make('odometro_inicial')
                                ->label('Odômetro Inicial (Km)')
                                ->numeric()
                                ->minValue(0)
                                ->dehydrated(true)
                                ->disabled(fn () => ! self::canFill('veiculos_condutores')),

                            \Filament\Forms\Components\TextInput::make('odometro_final')
                                ->label('Odômetro Final (Km)')
                                ->numeric()
                                ->minValue(0)
                                ->dehydrated(true)
                                ->reactive()
                                ->disabled(fn () => ! self::canFill('veiculos_condutores')),

                            \Filament\Forms\Components\Placeholder::make('distancia')
                                ->label('Distância Percorrida')
                                ->content(function (Get $get) {
                                    $i = (int) $get('odometro_inicial');
                                    $f = (int) $get('odometro_final');
                                    return $i && $f && $f >= $i ? (($f - $i) . ' Km') : '0 Km';
                                })
                                ->columnSpanFull(),
                        ])
                        ->afterStateHydrated(function (\Filament\Forms\Components\Repeater $component, ?Report $record) {
                            if ($record && $record->exists) {
                                $condutores = $record->condutores->map(function ($condutor) {
                                    return [
                                        'veiculo_id' => $condutor->veiculo_id,
                                        'motorista_id' => $condutor->motorista_id,
                                        'matricula' => \App\Models\User::find($condutor->motorista_id)?->matricula ?? null,
                                        'odometro_inicial' => $condutor->odometro_inicial,
                                        'odometro_final' => $condutor->odometro_final,
                                    ];
                                })->toArray();
                                $component->state($condutores);
                            }
                        })
                        ->disabled(fn () => ! self::canFill('veiculos_condutores')),

                ]),
            Section::make('Observações Gerais')
                ->visible(fn () => self::canSee('observacoes'))
                ->schema([
                    RichEditor::make('observacoes')
                        ->label('Observações Gerais')
                        ->toolbarButtons([
                            'bold','italic','underline','strike',
                            'bulletList','orderedList','blockquote',
                            'h2','h3','link','undo','redo',
                        ])
                        ->columnSpanFull()
                        ->dehydrated(true)
                        ->afterStateHydrated(function (RichEditor $c, ?\App\Models\Report $record) {
                            if ($record) $c->state($record->observacoes);
                        })
                        ->disabled(fn () => ! self::canFill('observacoes')),
                ]),

// 7 — Agente Relator
            Section::make('Agente Relator')
                ->visible(fn () => self::canSee('relator'))
                ->schema([
                    Select::make('relator_id')
                        ->label('Relator do relatório')
                        ->options(function (\Filament\Forms\Get $get, ?\App\Models\Report $record) {
                            // usa os usuários selecionados na seção Equipe
                            $ids = collect($get('team_ids') ?? [])
                                ->when(!$get('team_ids') && $record, fn($c) => $record->team()->pluck('users.id'));
                            return $ids->isEmpty()
                                ? []
                                : \App\Models\User::whereIn('id', $ids)->orderBy('name')->pluck('name','id');
                        })
                        ->searchable()
                        ->native(false)
                        ->required()
                        ->dehydrated(true)
                        ->afterStateHydrated(function (Select $c, ?\App\Models\Report $record) {
                            if ($record) $c->state($record->relator_id);
                        })
                        ->disabled(fn () => ! self::canFill('relator')),
                ]),

// 8 — Anexos (aparece/funciona melhor após salvar o relatório)
            Section::make('Anexos')
                ->visible(fn () => self::canSee('anexos'))
                ->hiddenOn('create') // << não renderiza na tela de criação
                ->schema([
                    \Filament\Forms\Components\Repeater::make('anexos')
                        ->relationship('anexos')
                        ->label('Arquivos')
                        ->addActionLabel('Adicionar arquivo')
                        ->orderable(false)
                        ->default([])                 // sem itens por padrão
                        ->columns(2)
                        ->disabled(fn () => ! self::canFill('anexos')) // só habilita para quem pode preencher
                        ->schema([
                            FileUpload::make('path')
                                ->label('Arquivo')
                                ->disk('public')
                                // se quiser já salvar por relatório:
                                ->directory(fn (? \App\Models\Report $record) => $record ? "reports/{$record->id}" : 'reports')
                                ->preserveFilenames()
                                ->openable()
                                ->downloadable()
                                ->previewable(true)
                                ->multiple(false)      // 1 arquivo por item do repeater
                                ->required(false)      // << remove a obrigatoriedade
                                ->acceptedFileTypes([
                                    'image/*', 'application/pdf',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/msword',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    'text/plain',
                                ]),

                            \Filament\Forms\Components\TextInput::make('original_name')
                                ->label('Nome original')
                                ->maxLength(255),

                            \Filament\Forms\Components\TextInput::make('mime')
                                ->label('MIME')
                                ->maxLength(120),

                            \Filament\Forms\Components\TextInput::make('size')
                                ->label('Tamanho (bytes)')
                                ->numeric()->minValue(0),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('start_at')->label('Início')->dateTime('d/m/Y H:i'),
                TextColumn::make('end_at')->label('Fim')->dateTime('d/m/Y H:i'),
                TextColumn::make('relator.name')->label('Relator')->toggleable(),
                TextColumn::make('anexos_count')->label('Anexos')->counts('anexos')->toggleable(),
                TextColumn::make('service_type')->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('shift')->label('Turno')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('author.name')->label('Criado por'),
                TextColumn::make('author.roles.name')
                    ->label('Perfis do Autor')
                    ->formatStateUsing(function ($record) {
                        return $record->author?->roles->pluck('name')->join(', ') ?? 'Nenhum';
                    })
                    ->toggleable()
                    ->searchable(),

            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->can('report.update')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->can('report.delete')),
                Action::make('pdf')
                    ->label('Baixar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn ($record) => route('reports.pdf', $record))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => auth()->user()->can('report.view'))
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit'   => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
