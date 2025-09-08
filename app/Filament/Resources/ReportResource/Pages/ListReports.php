<?php


namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;



class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected static ?string $navigationLabel = 'Relatórios Arquivados';

//    protected function getTitle(): string
//    {
//        return 'Relatórios Arquivados';
//    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('arquivados')
                ->label('Relatórios Arquivados')
                ->icon('heroicon-o-archive-box')
                ->visible(fn () => auth()->user()->can('report.viewArchived'))
                ->url(ReportResource::getUrl('arquivados')),
        ];
    }

    // (Opcional) Garanta zero filtros nesta página:
    protected function getTableFilters(): array
    {
        return [];
    }





}
