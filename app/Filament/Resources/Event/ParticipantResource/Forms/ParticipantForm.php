<?php

namespace App\Filament\Resources\Event\ParticipantResource\Forms;

use App\Enums\Participants\BloodType;
use App\Enums\Participants\Gender;
use App\Models\Event\Participant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;

class ParticipantForm
{
    public static function make(): array
    {
        return [
            Placeholder::make('schedule')
                ->label(__('event.participant_schedule'))
                ->content(fn (Participant $record): string => $record->schedule->title),

            Placeholder::make('package')
                ->label(__('event.participant_package'))
                ->content(fn (Participant $record): string => $record->package->title),

            Placeholder::make('bib')
                ->label(__('event.participant_bib'))
                ->content(fn (Participant $record): string => $record->bib),

            TextInput::make('name')
                ->label(__('event.participant_name'))
                ->required(),

            DatePicker::make('birthdate')
                ->label(__('event.participant_birthdate'))
                ->required(),

            Radio::make('gender')
                ->label(__('event.participant_gender'))
                ->options(Gender::options())
                ->enum(Gender::class)
                ->required(),

            Radio::make('blood_type')
                ->label(__('event.participant_blood_type'))
                ->options(BloodType::options())
                ->enum(BloodType::class)
                ->required(),

            Grid::make()
                ->schema([
                    TextInput::make('phone')
                        ->label(__('event.participant_phone'))
                        ->tel()
                        ->required(),

                    TextInput::make('email')
                        ->label(__('event.participant_email'))
                        ->nullable()
                        ->email(),
                ]),
        ];
    }
}
