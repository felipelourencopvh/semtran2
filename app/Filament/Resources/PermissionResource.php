<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;


class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationLabel = 'Permissões';

    public static function getPluralLabel(): string
    {
        return 'Permissões'; // Nome plural no título da listagem
    }

    public static function getModelLabel(): string
    {
        return 'Permissão'; // Nome singular em formulários e botões
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Permissão (ex: user.view)')
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('guard_name')
                ->default('web')
                ->disabled(),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nome')->searchable(),
            TextColumn::make('guard_name')->label('Guard'),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
