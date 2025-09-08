<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\RestoreAction;
use Illuminate\Contracts\Support\Htmlable;   // <— adicione

class ListArquivadosReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    // rótulos do menu (se quiser manter)
    protected static ?string $navigationLabel = 'Relatórios Arquivados';
    protected static ?string $navigationGroup = 'Operação';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?int $navigationSort = 3;

    // 🔹 breadcrumb específico da página
    protected static ?string $breadcrumb = 'Relatórios Arquivados';

    // 🔹 título/heading exibido (H1)
    public function getHeading(): string|Htmlable
    {
        return 'Relatórios Arquivados';
    }

    // (opcional) título da aba do navegador
    public function getTitle(): string|Htmlable
    {
        return 'Relatórios Arquivados';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('report.viewArchived');
    }

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return static::getResource()::getEloquentQuery()->onlyTrashed();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('ativos')
                ->label('Voltar para Ativos')
                ->icon('heroicon-o-list-bullet')
                ->url(ReportResource::getUrl('index')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            RestoreAction::make()->label('Desarquivar')->color('success'),
            Action::make('downloadPdf')
                ->label('Baixar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('reports.pdf', $record))
                ->openUrlInNewTab(),
        ];
    }
}
