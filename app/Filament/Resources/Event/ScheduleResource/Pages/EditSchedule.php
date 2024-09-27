<?php

namespace App\Filament\Resources\Event\ScheduleResource\Pages;

use App\Events\Event\ScheduleUpdated;
use App\Filament\Resources\Event\ScheduleResource;
use App\Models\URL\ShortURL;
use App\Models\User;
use AshAllenDesign\ShortURL\Exceptions\ShortURLException;
use AshAllenDesign\ShortURL\Facades\ShortURL as URLShortener;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            ActionGroup::make([
                Action::make('invite_user')
                    ->label(__('event.btn_invite_user'))
                    ->color('primary')
                    ->icon('heroicon-s-user')
                    ->form([
                        Select::make('users')
                            ->options(
                                User::query()
                                    ->where('id', '!=', Auth::id())
                                    ->pluck('name', 'id'),
                            )
                            ->searchable()
                            ->multiple()
                            ->required(),

                        TextInput::make('channels')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $this->record->users()->syncWithoutDetaching($data['users']);
                    }),

                DeleteAction::make(),
            ]),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->slug !== $data['slug']) {
            $this->record->slugs()->create([
                'origin' => $this->record->slug,
                'destination' => $data['slug'],
                'status' => Response::HTTP_MOVED_PERMANENTLY,
            ]);
        }

        if (empty($data['description'])) {
            $data['description'] = '';
        }

        if (! Str::isAscii($data['title'])) {
            $data['alias'] = Str::ascii($data['title']);
        }

        return $data;
    }

    /**
     * @throws ShortURLException
     */
    protected function afterSave(): void
    {
        // if someone is able to edit the schedule
        // it should be belonged to the user
        Auth::user()->schedules()->syncWithoutDetaching($this->record);

        event(new ScheduleUpdated($this->record));

        // clear cached response
        Cache::forget('response.'.sha1(route('schedule.view', $this->record->slug)));

        // clear pinned event
        Cache::forget('event.pinned');

        $exists = ShortURL::query()
            ->where('destination_url', $this->record->url)
            ->exists();

        if (! $exists) {
            URLShortener::destinationUrl($this->record->url)
                ->redirectStatusCode(302)
                ->trackVisits()
                ->make();
        }
    }
}
