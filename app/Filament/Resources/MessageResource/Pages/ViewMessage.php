<?php

namespace App\Filament\Resources\MessageResource\Pages;

use App\Filament\Resources\MessageResource;
use App\Filament\Resources\MessageResource\Enums\MessageStatus;
use App\Models\Message;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewMessage extends ViewRecord
{
    protected static string $resource = MessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_as_spam')
                ->visible(fn (Message $message): bool => $message->status !== MessageStatus::Spam)
                ->label(__('message.button_mark_as_spam'))
                ->action(function (Message $message): void {
                    $message->markAsSpam();

                    Notification::make('marked_as_spam')
                        ->title(__('message.marked_as_spam'))
                        ->success()
                        ->send();
                }),

            Actions\Action::make('ummark_as_spam')
                ->visible(fn (Message $message): bool => $message->status === MessageStatus::Spam)
                ->label(__('message.button_unmark_as_spam'))
                ->action(function (Message $message): void {
                    // alias for not spam
                    $message->markAsRead();

                    Notification::make('marked_as_spam')
                        ->title(__('message.unmarked_as_spam'))
                        ->success()
                        ->send();
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $message = self::getRecord();
        if ($message->status === MessageStatus::Unread) {
            $message->markAsRead();
        }

        return $infolist
            ->columns(3)
            ->schema([
                Section::make(__('message.title'))
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('subject')
                            ->hiddenLabel()
                            ->default(__('message.no_subject'))
                            ->size(TextEntry\TextEntrySize::Large),

                        TextEntry::make('body')
                            ->hiddenLabel()
                            ->html(),
                    ]),

                Section::make()
                    ->columnSpan(1)
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('message.name')),

                        TextEntry::make('email')
                            ->label(__('message.email'))
                            ->default('-'),

                        TextEntry::make('created_at')
                            ->label(__('ui.created_at'))
                            ->since(),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (Message $message): ?string => $message->status->color())
                            ->formatStateUsing(fn (Message $message): ?string => $message->status->label()),
                    ]),
            ]);
    }
}
