<?php

namespace App\Filament\Clusters\Databases\Pages;

use App\Filament\Clusters\Databases;
use App\Filament\Clusters\Databases\Enums\DatabasePermission;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
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

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;

    protected string $view = 'filament.clusters.databases.pages.backup';

    protected static ?string $cluster = Databases::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('database.file.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('database.file.label');
    }

    public static function canAccess(): bool
    {
        return user_can(DatabasePermission::Browse);
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function (): array {
                return collect(Storage::disk('local')->allFiles('database'))
                    ->map(function ($file): array {
                        [$name, $ext] = explode('.', basename($file), 2);

                        $createdAt = Carbon::createFromTimestamp(intval($name));

                        return [
                            'id' => $name,
                            'path' => $file,
                            'name' => sprintf('%s.%s', $name, $ext),
                            'extension' => $ext,
                            'size' => Storage::size($file),
                            'created_at' => $createdAt,
                        ];
                    })
                    ->toArray();
            })
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->label(__('database.file.name')),

                TextColumn::make('size')
                    ->label(__('database.size'))
                    ->formatStateUsing(fn (array $record): string => Number::fileSize($record['size'])),

                TextColumn::makeSinceDate('created_at', __('ui.created_at')),
            ])
            ->recordActions([
                Action::make('download')
                    ->visible(user_can(DatabasePermission::Download))
                    ->label(__('ui.btn.download'))
                    ->button()
                    ->color('primary')
                    ->action(function (?array $record = null): ?StreamedResponse {
                        if (empty($record)) {
                            return null;
                        }

                        $storage = Storage::disk('local');

                        if ($storage->exists($record['path'])) {
                            return response()->streamDownload(function () use ($storage, $record) {
                                $stream = $storage->readStream($record['path']);
                                while (! feof($stream)) {
                                    echo fread($stream, 8192);
                                }
                                fclose($stream);
                            }, $record['name']);
                        }

                        Notification::make('file_not_exists')
                            ->warning()
                            ->title(__('database.file_not_exist'))
                            ->send();

                        return null;
                    }),

                Action::make('delete')
                    ->visible(user_can(DatabasePermission::Delete))
                    ->label(__('ui.btn.delete'))
                    ->button()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (?array $record = null): void {
                        if ($record === null) {
                            return;
                        }

                        try {
                            $storage = Storage::disk('local');
                            $storage->delete($record['path']);

                            Notification::make('database_file_deleted')
                                ->title(__('database.file_deleted'))
                                ->success()
                                ->send();

                            return;
                        } catch (Exception $e) {
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
