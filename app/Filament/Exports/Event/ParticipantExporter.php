<?php

namespace App\Filament\Exports\Event;

use App\Enums\Participants\BloodType;
use App\Enums\Participants\Gender;
use App\Enums\Participants\IdentityType;
use App\Enums\Participants\Relation;
use App\Models\Event\Participant;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ParticipantExporter extends Exporter
{
    protected static ?string $model = Participant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID')
                ->enabledByDefault(false),

            ExportColumn::make('user.name')
                ->enabledByDefault(false)
                ->label(__('user.name')),

            ExportColumn::make('schedule.title')
                ->enabledByDefault(false)
                ->label(__('event.participant_schedule')),

            ExportColumn::make('package.title'),

            ExportColumn::make('bib')
                ->label(__('event.participant_bib')),

            ExportColumn::make('id_type')
                ->enabledByDefault(false)
                ->label(__('event.participant_id_type'))
                ->formatStateUsing(fn (IdentityType $state): string => $state->label()),

            ExportColumn::make('id_number')
                ->enabledByDefault(false)
                ->label(__('event.participant_id_number')),

            ExportColumn::make('name')
                ->label(__('event.participant_name')),

            ExportColumn::make('birthdate')
                ->label(__('event.participant_birthdate'))
                ->formatStateUsing(fn (Carbon $state): string => $state->toDateString()),

            ExportColumn::make('gender')
                ->label(__('event.participant_gender'))
                ->formatStateUsing(fn (Gender $state): string => $state->label()),

            ExportColumn::make('blood_type')
                ->formatStateUsing(fn (BloodType $state): string => $state->label()),

            ExportColumn::make('phone')
                ->enabledByDefault(false)
                ->label(__('event.participant_phone')),

            ExportColumn::make('email')
                ->label(__('event.participant_email'))
                ->enabledByDefault(false),

            ExportColumn::make('emergency_name')
                ->label(__('event.participant_emergency_name')),

            ExportColumn::make('emergency_phone')
                ->label(__('event.participant_emergency_phone')),

            ExportColumn::make('emergency_relation')
                ->enabledByDefault(false)
                ->label(__('event.participant_emergency_relation'))
                ->formatStateUsing(fn (Relation $state): string => $state->label()),

            ExportColumn::make('status')
                ->enabledByDefault(false)
                ->label(__('event.participant_status')),

            ExportColumn::make('created_at')
                ->enabledByDefault(false)
                ->label(__('ui.created_at')),

            ExportColumn::make('updated_at')
                ->enabledByDefault(false)
                ->label(__('ui.updated_at')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your participant export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
