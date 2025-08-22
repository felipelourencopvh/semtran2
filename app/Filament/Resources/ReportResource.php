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
                            ->disabled(fn () => ! self::canFill('informacoes_gerais')),
                        Select::make('shift')
                            ->label('Turno')
                            ->options([
                                'plantao' => 'Plantão',
                                'manha' => 'Manhã',
                                'tarde' => 'Tarde',
                                'noite' => 'Noite',
                            ])
                            ->required()
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
                        ->dehydrated(false) // não tenta salvar na tabela reports
                        ->afterStateHydrated(function ($component, ?Report $record) {
                            // Preenche o campo quando estiver editando
                            if ($record && $record->exists) {
                                $component->state($record->team()->pluck('users.id')->all());
                            }
                        }),

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
