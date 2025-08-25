<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Department;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Usuários';
    public static function getPluralLabel(): string
    {
        return 'Usuários'; // Nome plural no título da listagem
    }

    public static function getModelLabel(): string
    {
        return 'Usuário'; // Nome singular em formulários e botões
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('Nome')->required(),
            TextInput::make('email')->email()->unique(ignoreRecord: true)->required(),
            TextInput::make('telefone')
                ->label('Telefone')
                ->placeholder('(69) 99999-9999')
                ->maxLength(20),

            TextInput::make('matricula')
                ->label('Matrícula')
                ->maxLength(30)
                ->unique(ignoreRecord: true),

            TextInput::make('nome_farda')
                ->label('Nome da Farda')
                ->maxLength(120),

            TextInput::make('password')
                ->password()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context) => $context === 'create'),

            Select::make('department_id')
                ->label('Departamento')
                ->relationship('department', 'name')
                ->searchable()
                ->preload()
                ->required(),

            // Multi perfis (roles)
            Select::make('roles')
                ->label('Perfis (Roles)')
                ->relationship('roles', 'name') // do trait HasRoles
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),

                Tables\Columns\TextColumn::make('telefone')
                    ->label('Telefone')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nome_farda')
                    ->label('Nome da Farda')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departamento')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Perfis')
                    ->formatStateUsing(function ($record) {
                        return $record->roles->pluck('name')->join(', ');
                    })
                    ->toggleable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
