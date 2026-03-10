<?php

namespace Modules\Job\Panel\Resources\FailedJobResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Modules\Job\Models\FailedJob;
use Modules\Job\Panel\Resources\FailedJobResource;
use Modules\Job\Panel\Resources\FailedJobResource\Enums\FailedJobPermission;

class ListFailedJobs extends ListRecords
{
    protected static string $resource = FailedJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retry')
                ->label(__('job::job.btn.retry_all'))
                ->requiresConfirmation()
                ->visible(user_can(FailedJobPermission::Retry))
                ->disabled(FailedJob::query()->count() <= 0)
                ->action(function (): void {
                    Artisan::call('queue:retry all');

                    Notification::make()
                        ->success()
                        ->title(__('job::job.pushed_to_queue'))
                        ->send();
                }),
        ];
    }
}
