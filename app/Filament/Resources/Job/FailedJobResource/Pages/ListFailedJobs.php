<?php

namespace App\Filament\Resources\Job\FailedJobResource\Pages;

use App\Filament\Resources\Job\FailedJobResource;
use App\Filament\Resources\Job\FailedJobResource\Enums\FailedJobPermission;
use App\Models\FailedJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListFailedJobs extends ListRecords
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry')
                ->label(__('job.btn.retry_all'))
                ->requiresConfirmation()
                ->visible(user_can(FailedJobPermission::Retry))
                ->disabled(FailedJob::query()->count() <= 0)
                ->action(function (): void {
                    Artisan::call('queue:retry all');

                    Notification::make('pushed_to_queue')
                        ->success()
                        ->title(__('job.pushed_to_queue'))
                        ->send();
                }),
        ];
    }
}
