<?php

namespace App\Filament\Resources\Job;

use App\Filament\Resources\Job\FailedJobResource\Enums\FailedJobPermission;
use App\Filament\Resources\Job\FailedJobResource\Pages\ListFailedJobs;
use App\Models\FailedJob;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.job');
    }

    public static function getLabel(): ?string
    {
        return __('job.failed_job');
    }

    public static function canAccess(): bool
    {
        return user_can(FailedJobPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('queue.default') === 'database';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('queue')
                    ->label(__('job.queue')),

                TextColumn::make('failed_at')
                    ->label(__('job.failed_at'))
                    ->date(get_datetime_format(), get_timezone()),

                TextColumn::make('exception')
                    ->label(__('job.exception'))
                    ->formatStateUsing(function (FailedJob $job): ?string {
                        return Str::of($job->exception)
                            ->explode(PHP_EOL)
                            ->first();
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('retry')
                    ->label(__('job.btn_retry'))
                    ->requiresConfirmation()
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->visible(user_can(FailedJobPermission::Retry))
                    ->action(function (FailedJob $record): void {
                        Artisan::call('queue:retry', ['id' => $record->id]);

                        Notification::make('pushed_to_queue')
                            ->success()
                            ->title(__('job.pushed_to_queue'))
                            ->send();
                    }),

                ActionGroup::make([
                    DeleteAction::make('delete')
                        ->visible(user_can(FailedJobPermission::Delete))
                        ->requiresConfirmation(),
                ]),
            ])
            ->toolbarActions([
                BulkAction::make('retry_selected')
                    ->label(__('job.btn_retry_selected'))
                    ->visible(user_can(FailedJobPermission::Retry))
                    ->requiresConfirmation()
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->action(function (Collection $records): void {
                        Artisan::call('queue:retry', ['id' => $records->pluck('id')->toArray()]);

                        Notification::make('pushed_to_queue')
                            ->success()
                            ->title(__('job.pushed_to_queue'))
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFailedJobs::route('/'),
        ];
    }
}
