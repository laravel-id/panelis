<?php

namespace App\Filament\Resources\Event\ParticipantResource\Forms;

use App\Enums\Participants\Relation;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;

class EmergencyContactForm
{
    public static function make(): array
    {
        return [
            Grid::make()
                ->schema([
                    TextInput::make('emergency_name')
                        ->label(__('event.participant_emergency_name'))
                        ->required(),

                    TextInput::make('emergency_phone')
                        ->label(__('event.participant_emergency_phone'))
                        ->tel()
                        ->required(),
                ]),

            Radio::make('emergency_relation')
                ->label(__('event.participant_emergency_relation'))
                ->options(Relation::options())
                ->enum(Relation::class)
                ->required(),
        ];
    }
}
