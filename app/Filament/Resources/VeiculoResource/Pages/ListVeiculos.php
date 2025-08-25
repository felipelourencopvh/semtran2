<?php

namespace App\Filament\Resources\VeiculoResource\Pages;

use App\Filament\Resources\VeiculoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVeiculos extends ListRecords
{
    protected static string $resource = VeiculoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
