<?php

namespace Modules\Job\Panel\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Job\Models\Job;
use Modules\Job\Panel\Resources\JobResource\Enums\JobPermission;
use Modules\Job\Panel\Resources\JobResource\Pages\ListJobs;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static bool $isScopedToTenant = false;

    public static function getNavigationGroup(): ?string
    {
        return __('job::job.label');
    }

    public static function getLabel(): ?string
    {
        return __('job::job.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('job::job.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(JobPermission::Browse);
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
                    ->label(__('job::job.queue')),

                TextColumn::make('attempts')
                    ->label(__('job::job.attempts')),

                TextColumn::make('reserved_at')
                    ->label(__('job::job.reserved_at'))
                    ->date(get_datetime_format()),

                TextColumn::make('available_at')
                    ->label(__('job::job.available_at'))
                    ->date(get_datetime_format()),

                TextColumn::makeSinceDate('created_at', __('ui.created_at')),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
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
            'index' => ListJobs::route('/'),
        ];
    }
}
