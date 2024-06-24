<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Enums\MessageStatus;
use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    public static function getLabel(): ?string
    {
        return __('message.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.message');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(function (Message $message): string {
                return Pages\ViewMessage::getUrl([$message->id]);
            })
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('message.name'))
                    ->description(fn (Message $message): ?string => $message->email)
                    ->searchable(),

                TextColumn::make('subject')
                    ->label(__('message.subject'))
                    ->weight(function (Message $message): FontWeight {
                        if ($message->status === MessageStatus::Unread) {
                            return FontWeight::Bold;
                        }

                        return FontWeight::Light;
                    })
                    ->searchable(),

                TextColumn::make('body')
                    ->label(__('message.body'))
                    ->words(10)
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('message.status'))
                    ->options(MessageStatus::options()),
            ])
            ->actions([
                Action::make('mark_as_read')
                    ->label(__('message.button_mark_as_read'))
                    ->disabled(fn (Message $message): bool => $message->status !== MessageStatus::Unread)
                    ->icon('heroicon-o-envelope-open')
                    ->action(function (Message $message): void {
                        $message->status = MessageStatus::Read;
                        $message->save();

                        Notification::make('marked_as_read')
                            ->title(__('message.marked_as_read'))
                            ->success()
                            ->send();
                    }),

                ActionGroup::make([
                    Action::make('mark_as_spam')
                        ->label(__('message.button_mark_as_spam'))
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('warning')
                        ->action(function (Message $message): void {
                            $message->status = MessageStatus::Spam;
                            $message->save();

                            Notification::make('marked_as_spam')
                                ->title(__('message.marked_as_spam'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_as_read')
                        ->label(__('message.button_mark_as_read'))
                        ->icon('heroicon-o-envelope-open')
                        ->action(function (Collection $records): void {
                            $records->each(function (Message $message): void {
                                $message->status = MessageStatus::Read;
                                $message->save();
                            });

                            Notification::make('marked_as_read')
                                ->title(__('message.marked_as_read'))
                                ->success()
                                ->send();
                        }),

                    BulkAction::make('mark_as_spam')
                        ->label(__('message.button_mark_as_spam'))
                        ->requiresConfirmation()
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(function (Message $message): void {
                                $message->status = MessageStatus::Spam;
                                $message->save();
                            });

                            Notification::make('marked_as_spam')
                                ->title(__('message.marked_as_spam'))
                                ->success()
                                ->send();
                        }),

                    DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
            'view' => Pages\ViewMessage::route('/{record}'),
        ];
    }
}
