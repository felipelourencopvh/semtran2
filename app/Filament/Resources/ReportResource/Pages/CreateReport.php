<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Consolida start_at e end_at a partir dos campos condicionais
        if (($data['same_day'] ?? true) === true) {
            $start = Carbon::parse(($data['date_single'] ?? now()->toDateString()) . ' ' . ($data['time_start_single'] ?? '00:00'));
            $end   = Carbon::parse(($data['date_single'] ?? now()->toDateString()) . ' ' . ($data['time_end_single'] ?? '00:00'));
        } else {
            $start = Carbon::parse(($data['date_start'] ?? now()->toDateString()) . ' ' . ($data['time_start'] ?? '00:00'));
            $end   = Carbon::parse(($data['date_end'] ?? now()->toDateString()) . ' ' . ($data['time_end'] ?? '00:00'));
        }

        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages([
                'end_at' => 'O término deve ser maior que o início.',
            ]);
        }

        $data['user_id'] = auth()->id();
        $data['start_at'] = $start;
        $data['end_at'] = $end;

        // Limpamos campos auxiliares usados só na UI
        unset(
            $data['date_single'], $data['time_start_single'], $data['time_end_single'],
            $data['date_start'], $data['time_start'], $data['date_end'], $data['time_end']
        );

        return $data;
    }
    protected function afterCreate(): void
    {
        $state = $this->form->getRawState(); // << pega TUDO, inclusive não-dehydrated
        $ids   = collect($state['team_ids'] ?? [])->filter()->all();

        $this->record->team()->sync($ids);
    }


}
