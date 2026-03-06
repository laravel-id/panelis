<?php

namespace App\Filament\Clusters\Settings;

use App\Events\SettingUpdated;
use App\Models\Setting;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class UpdateSettingPage extends Page
{
    protected BackedEnum $permission;

    public function update(): void
    {
        abort_unless(user_can(static::updatePermission()), Response::HTTP_FORBIDDEN);

        $states = $this->form->getState();

        $this->beforeValidate($states);

        $this->validate();

        $this->afterValidated($states);

        try {
            foreach (Arr::dot($states) as $key => $value) {
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            event(new SettingUpdated);

            Notification::make('setting_updated')
                ->title(__('setting.notifications.updated.title'))
                ->success()
                ->send();

            $this->afterUpdated($states);
        } catch (Throwable $e) {
            Log::error($e);

            Notification::make('setting_not_updated')
                ->title(__('setting.notifications.update_failed.title'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function beforeValidate(array $forms): void {}

    protected function afterValidated(array $forms): void {}

    protected function afterUpdated(array $forms): void {}
}
