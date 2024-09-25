<?php

namespace App\Filament\Resources\Event\ParticipantResource\Pages;

use App\Filament\Resources\Event\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParticipant extends EditRecord
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
