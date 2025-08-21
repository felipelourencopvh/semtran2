<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Spatie\Permission\Models\Role;          // <-- CORRETO
use Spatie\Permission\Models\Permission;    // (só se usar options() manual)
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;



class RoleResource extends Resource
{
    protected static ?string $model = Role::class; // <-- CORRETO
    protected static ?string $navigationLabel = 'Perfis';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPluralLabel(): string
    {
        return 'Perfis';
    }

    public static function getModelLabel(): string
    {
        return 'Perfil';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Nome do Perfil')
                ->required()
                ->unique(ignoreRecord: true),

            CheckboxList::make('permissions')
                ->label('Permissões')
                ->relationship('permissions', 'name')
                ->columns(2)
                ->bulkToggleable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Perfil')->searchable(),
            TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
        ])->actions([
            \Filament\Tables\Actions\EditAction::make(),
        ])->bulkActions([
            \Filament\Tables\Actions\BulkActionGroup::make([
                \Filament\Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
