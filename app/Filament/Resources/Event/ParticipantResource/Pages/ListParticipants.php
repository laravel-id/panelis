<?php

namespace App\Filament\Resources\Event\ParticipantResource\Pages;

use App\Filament\Resources\Event\ParticipantResource;
use Filament\Resources\Pages\ListRecords;

class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
