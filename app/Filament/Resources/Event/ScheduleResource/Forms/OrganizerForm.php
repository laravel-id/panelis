<?php

namespace App\Filament\Resources\Event\ScheduleResource\Forms;

use App\Filament\Resources\Event\OrganizerResource\Forms\OrganizerForm as CreateForm;
use App\Models\Event\Organizer;
use Filament\Forms\Components\Select;

class OrganizerForm
{
    public static function schema(): array
    {
        return [
            Select::make('organizers')
                ->relationship('organizers', ignoreRecord: true)
                ->native(false)
                ->multiple()
                ->searchable(['name', 'email'])
                ->preload()
                ->options(Organizer::options())
                ->createOptionForm(CreateForm::schema()),
        ];
    }
}
