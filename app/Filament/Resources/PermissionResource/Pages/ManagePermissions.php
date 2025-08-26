<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Actions\User\BackupPermission;
use App\Actions\User\SeedPermission;
use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\PermissionResource\Enums\Permission;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ManagePermissions extends ManageRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(user_can(Permission::Create))
                ->mutateFormDataUsing(function (array $data): array {
                    $key = Str::snake($data['name']);

                    $data['label'] = "user.permission_name_{$key}";
                    $data['description'] = "user.permission_description_{$key}";

                    return $data;
                }),

            ActionGroup::make([
                Action::make('generate_permission')
                    ->visible(user_can(Permission::Create))
                    ->label(__('user.btn_generate_permission'))
                    ->requiresConfirmation()
                    ->visible(user_can(Permission::Create))
                    ->action(function (): void {
                        SeedPermission::run();
                    }),

                Action::make('backup_permission')
                    ->visible(user_can(Permission::Backup))
                    ->label(__('user.btn_backup_permission'))
                    ->requiresConfirmation()
                    ->action(function (): void {
                        $path = BackupPermission::run();

                        Notification::make('permission_stored')
                            ->title(__('user.permission_backed_up'))
                            ->body(__('user.permission_backed_up_body', ['path' => $path]))
                            ->success()
                            ->send();
                    }),
            ]),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(
            user_can(Permission::Browse) || user_can(Permission::Read),
            Response::HTTP_FORBIDDEN,
        );
    }
}
