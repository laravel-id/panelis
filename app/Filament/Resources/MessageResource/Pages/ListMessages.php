<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use App\Models\Message;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMessages extends ListRecords
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('message.tab_all'))
                ->icon('heroicon-o-envelope-open')
                ->badge(
                    Message::query()
                        ->where('status', '!=', MessageResource\Enums\MessageStatus::Spam)
                        ->count()
                )
                ->modifyQueryUsing(function (Builder $query): Builder {
                    return $query->where('status', '!=', MessageResource\Enums\MessageStatus::Spam);
                }),

            'unread' => Tab::make(__('message.tab_unread'))
                ->icon('heroicon-o-envelope')
                ->badge(Message::unread()->count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->unread()),

            'spam' => Tab::make(__('message.tab_spam'))
                ->icon('heroicon-o-exclamation-triangle')
                ->badge(Message::spam()->count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->spam()),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        if (Message::unread()->count() >= 1) {
            return 'unread';
        }

        return 'all';
    }
}
