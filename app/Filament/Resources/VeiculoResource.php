<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VeiculoResource\Pages;
use App\Models\Department;
use App\Models\Veiculo;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MultiSelect;   // <-- IMPORTANTE
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Veículos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('placa')
                    ->label('Placa')
                    ->required()
                    // garante unique na tabela de veículos ignorando o registro atual
                    ->unique(table: Veiculo::class, column: 'placa', ignoreRecord: true),

                TextInput::make('modelo')->label('Modelo')->required(),
                TextInput::make('marca')->label('Marca')->required(),
                TextInput::make('especie')->label('Espécie')->required(),

                TextInput::make('odometro_atual')
                    ->label('Odômetro atual')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),

                Select::make('department_owner_id')
                    ->label('Departamento Proprietário')
                    ->options(fn () => Department::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                MultiSelect::make('departamentosAutorizados')
                    ->label('Departamentos que podem usar')
                    ->relationship('departamentosAutorizados', 'name') // belongsToMany no model
                    ->preload()
                    ->searchable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('placa')->label('Placa')->searchable()->sortable(),
                TextColumn::make('modelo')->label('Modelo')->searchable()->sortable(),
                TextColumn::make('marca')->label('Marca')->sortable(),
                TextColumn::make('especie')->label('Espécie')->sortable(),
                TextColumn::make('odometro_atual')->label('Odômetro')->sortable(),

                // aqui muda:
                TextColumn::make('proprietario.name')
                    ->label('Depto. Proprietário')
                    ->default('-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('departamentosAutorizados.name')
                    ->label('Deptos. Autorizados')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->wrap(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

// para evitar N+1:
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('proprietario'); // <-- aqui também
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVeiculos::route('/'),
            'create' => Pages\CreateVeiculo::route('/create'),
            'edit'   => Pages\EditVeiculo::route('/{record}/edit'),
        ];
    }
}
