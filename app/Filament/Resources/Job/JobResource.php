<?php

namespace app\Filament\Resources\Job;

use app\Filament\Resources\Job\JobResource\Enums\JobPermission;
use App\Models\Job;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.job');
    }

    public static function getLabel(): ?string
    {
        return __('job.job');
    }

    public static function canAccess(): bool
    {
        return user_can(JobPermission::Browse);
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

                TextColumn::make('attempts')
                    ->label(__('job.attempts')),

                TextColumn::make('reserved_at')
                    ->label(__('job.reserved_at'))
                    ->date(get_datetime_format(), get_timezone()),

                TextColumn::make('available_at')
                    ->label(__('job.available_at'))
                    ->date(get_datetime_format(), get_timezone()),

                TextColumn::make('created_at')
                    ->label(__('job.created_at'))
                    ->since(get_timezone()),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([

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
            'index' => JobResource\Pages\ListJobs::route('/'),
        ];
    }
}
