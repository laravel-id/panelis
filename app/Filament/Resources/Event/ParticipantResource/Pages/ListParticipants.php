<?php

namespace App\Filament\Resources\Event\ParticipantResource\Pages;

use App\Filament\Exports\Event\ParticipantExporter;
use App\Filament\Resources\Event\ParticipantResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->label(__('event.btn_export_participant'))
                ->color('primary')
                ->exporter(ParticipantExporter::class),
        ];
    }
}
