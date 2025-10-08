<?php

namespace App\Filament\Resources\Job;

use App\Filament\Resources\Job\FailedJobResource\Enums\FailedJobPermission;
use App\Models\FailedJob;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
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
        return __('job.label');
    }

    public static function getLabel(): ?string
    {
        return __('job.failed.label');
    }

    public static function canAccess(): bool
    {
        return user_can(FailedJobPermission::Browse);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('queue.default') === 'database';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->label(__('job.failed.failed_at'))
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
            ->actions([
                Action::make('retry')
                    ->label(__('job.btn.retry'))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-arrow-path')
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
            ->bulkActions([
                BulkAction::make('retry_selected')
                    ->label(__('job.btn.retry_selected'))
                    ->visible(user_can(FailedJobPermission::Retry))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-arrow-path')
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
            'index' => FailedJobResource\Pages\ListFailedJobs::route('/'),
        ];
    }
}
