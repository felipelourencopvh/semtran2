<?php


namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;



class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationLabel = 'Relatórios';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Operação';

    // Helpers de permissão por seção
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
                        ->afterStateHydrated(function ($component, ?Report $record) {
                            if ($record && $record->exists) {
                                $same = $record->same_day
                                    ?? $record->start_at?->toDateString() === $record->end_at?->toDateString();
                                $component->state($same);
                            }
                        })
                        ->disabled(fn () => ! self::canFill('informacoes_gerais')),

                    // Mesmo dia ON
                    Grid::make(3)
                        ->visible(fn (Get $get) => $get('same_day') === true)
                        ->schema([
                            DatePicker::make('date_single')
                                ->label('Data')
                                ->required(fn (Get $get) => $get('same_day') === true)
                                ->afterStateHydrated(function ($component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->toDateString());
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_start_single')
                                ->label('Hora inicial')
                                ->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === true)
                                ->afterStateHydrated(function ($component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_end_single')
                                ->label('Hora final')
                                ->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === true)
                                ->afterStateHydrated(function ($component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->end_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                        ]),

                    // Mesmo dia OFF
                    Grid::make(4)
                        ->visible(fn (Get $get) => $get('same_day') === false)
                        ->schema([
                            DatePicker::make('date_start')->label('Data inicial')
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (\Filament\Forms\Components\DatePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->toDateString());
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_start')->label('Hora inicial')->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (\Filament\Forms\Components\TimePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->start_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            DatePicker::make('date_end')->label('Data final')
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (\Filament\Forms\Components\DatePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->end_at?->toDateString());
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                            TimePicker::make('time_end')->label('Hora final')->seconds(false)
                                ->required(fn (Get $get) => $get('same_day') === false)
                                ->afterStateHydrated(function (\Filament\Forms\Components\TimePicker $component, ?Report $record) {
                                    if ($record && $record->exists) {
                                        $component->state($record->end_at?->format('H:i'));
                                    }
                                })
                                ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                        ]),

                    // Tipo / Turno (semelhantes ao que você já tem)
                    Grid::make(2)->schema([
                        Select::make('service_type')
                            ->label('Tipo de Serviço')
                            ->options([
                                'ordinario' => 'Ordinário',
                                'extraordinario' => 'Extraordinário',
                            ])
                            ->required()
                            ->dehydrated(true) // garante que o valor permanece no estado após salvar
                            ->afterStateHydrated(function ($component, ?\App\Models\Report $record) {
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
                            ->afterStateHydrated(function ($component, ?\App\Models\Report $record) {
                                if ($record) {
                                    $component->state($record->shift);
                                }
                            })
                            ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                    ])

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
                        ->dehydrated(false) // não tenta salvar na tabela reports
                        ->afterStateHydrated(function ($component, ?Report $record) {
                            // Preenche o campo quando estiver editando
                            if ($record && $record->exists) {
                                $component->state($record->team()->pluck('users.id')->all());
                            }
                        }),

                ]),
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
                                ->options(fn () => \App\Models\TipoAtividade::orderBy('nome')->pluck('nome','id'))
                                ->searchable()->reactive()->required()
                                ->disabled(fn () => ! self::canFill('descricao_atividades'))
                                ->afterStateUpdated(fn ($state, callable $set) => [
                                    $set('situacao_atividade_id', null),
                                    $set('medida_atividade_id', null),
                                ]),

                            Select::make('situacao_atividade_id')
                                ->label('Situação')
                                ->options(function (\Filament\Forms\Get $get) {
                                    $tipoId = $get('tipo_atividade_id');
                                    return $tipoId
                                        ? \App\Models\SituacaoAtividade::where('tipo_atividade_id', $tipoId)->orderBy('nome')->pluck('nome','id')
                                        : [];
                                })
                                ->searchable()->reactive()->required()
                                ->disabled(fn () => ! self::canFill('descricao_atividades'))
                                ->afterStateUpdated(fn ($state, callable $set) => $set('medida_atividade_id', null)),

                            Select::make('medida_atividade_id')
                                ->label('Medidas Adotadas')
                                ->options(function (\Filament\Forms\Get $get) {
                                    $sitId = $get('situacao_atividade_id');
                                    return $sitId
                                        ? \App\Models\MedidaAtividade::where('situacao_atividade_id', $sitId)->orderBy('nome')->pluck('nome','id')
                                        : [];
                                })
                                ->searchable()->required()
                                ->disabled(fn () => ! self::canFill('descricao_atividades')),

                            \Filament\Forms\Components\TextInput::make('endereco')
                                ->label('Endereço')
                                ->placeholder('Insira o endereço...')
                                ->columnSpanFull()->maxLength(255)
                                ->disabled(fn () => ! self::canFill('descricao_atividades')),
                        ])
                        ->disabled(fn () => ! self::canFill('descricao_atividades')),

                    \Filament\Forms\Components\Textarea::make('descricao_manual')
                        ->label('3.2 Descrição Manual das Atividades')
                        ->placeholder('Ex.: Rondas na área central, verificação de semáforos na zona leste, apoio a eventos, etc...')
                        ->rows(6)
                        // garante que o valor sempre entra/permanece no estado do form
                        ->dehydrated(true)
                        ->afterStateHydrated(function ($component, ?\App\Models\Report $record) {
                            if ($record) {
                                $component->state($record->descricao_manual);
                            }
                        })
                        ->disabled(fn () => ! self::canFill('descricao_atividades')),

                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),

                TextColumn::make('start_at')
                    ->label('Início')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('end_at')
                    ->label('Fim')
                    ->dateTime('d/m/Y H:i'),

                TextColumn::make('service_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('shift')
                    ->label('Turno')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('author.name')->label('Criado por'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->can('report.update')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->can('report.delete')),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
