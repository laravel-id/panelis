<?php

namespace App\Filament\Clusters\Databases\Pages;

use App\Filament\Clusters\Databases;
use App\Filament\Clusters\Databases\Enums\DatabasePermission;
use App\Models\Database;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Backup extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static string $view = 'filament.clusters.databases.pages.backup';

    protected static ?string $cluster = Databases::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('navigation.database_file');
    }

    public function getTitle(): string|Htmlable
    {
        return __('database.backup');
    }

    public static function canAccess(): bool
    {
        return user_can(DatabasePermission::Browse);
    }

    public function table(Table $table): Table
    {
        $query = Database::query()
            ->orderByDesc('id');

        return $table
            ->query($query)
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label(__('database.file_name')),

                TextColumn::make('size')
                    ->label(__('database.size'))
                    ->formatStateUsing(fn (Database $db): ?string => Number::fileSize($db->size)),

                TextColumn::make('created_at')
                    ->label(__('database.created_at'))
                    ->since(get_timezone())
                    ->dateTimeTooltip(get_datetime_format(), get_timezone()),
            ])
            ->actions([
                Action::make('download')
                    ->visible(user_can(DatabasePermission::Download))
                    ->label(__('database.button_download'))
                    ->button()
                    ->color('primary')
                    ->action(function (Database $db): ?StreamedResponse {
                        $storage = Storage::disk('local');

                        if ($storage->exists($db->path)) {
                            return response()->streamDownload(function () use ($storage, $db) {
                                $stream = $storage->readStream($db->path);
                                while (! feof($stream)) {
                                    echo fread($stream, 8192);
                                }
                                fclose($stream);
                            }, $db->name);
                        }

                        Notification::make('file_not_exists')
                            ->warning()
                            ->title(__('database.file_not_exist'))
                            ->send();

                        return null;
                    }),

                Action::make('delete')
                    ->visible(user_can(DatabasePermission::Delete))
                    ->label(__('database.button_delete'))
                    ->button()
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Database $db): void {
                        try {
                            $storage = Storage::disk('local');
                            $storage->delete($db->path);

                            Notification::make('database_file_deleted')
                                ->title(__('database.file_deleted'))
                                ->success()
                                ->send();

                            return;
                        } catch (\Exception $e) {
                            Log::error($e);
                        }

                        Notification::make('database_file_not_deleted')
                            ->title(__('database.file_not_deleted'))
                            ->warning()
                            ->send();
                    }),
            ]);
    }
}
