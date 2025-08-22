<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Mesmo algoritmo da criação
        if (($data['same_day'] ?? true) === true) {
            $start = Carbon::parse(($data['date_single'] ?? $this->record->start_at->toDateString()) . ' ' . ($data['time_start_single'] ?? $this->record->start_at->format('H:i')));
            $end   = Carbon::parse(($data['date_single'] ?? $this->record->end_at->toDateString())   . ' ' . ($data['time_end_single'] ?? $this->record->end_at->format('H:i')));
        } else {
            $start = Carbon::parse(($data['date_start'] ?? $this->record->start_at->toDateString()) . ' ' . ($data['time_start'] ?? $this->record->start_at->format('H:i')));
            $end   = Carbon::parse(($data['date_end'] ?? $this->record->end_at->toDateString())   . ' ' . ($data['time_end'] ?? $this->record->end_at->format('H:i')));
        }

        if ($end->lessThanOrEqualTo($start)) {
            throw ValidationException::withMessages([
                'end_at' => 'O término deve ser maior que o início.',
            ]);
        }

        $data['start_at'] = $start;
        $data['end_at']   = $end;

        unset(
            $data['date_single'], $data['time_start_single'], $data['time_end_single'],
            $data['date_start'], $data['time_start'], $data['date_end'], $data['time_end']
        );

        return $data;
    }
    protected function afterSave(): void
    {
        $state = $this->form->getRawState(); // inclui campos non-dehydrated
        $ids   = collect($state['team_ids'] ?? [])->filter()->map(fn ($v) => (int) $v)->all();

        // 1) Salva o pivot
        $this->record->team()->sync($ids);

        // 2) Recarrega a relação e reatribui o estado do campo (como ele é non-dehydrated)
        $this->record->unsetRelation('team');
        $this->record->load('team');

        $selected = $this->record->team()->pluck('users.id')->all();
        $this->form->fill([
            'team_ids' => $selected,
        ]);
    }



}
