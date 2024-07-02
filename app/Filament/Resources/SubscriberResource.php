<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

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
            ->recordUrl(null)
            ->columns([
                IconColumn::make('is_subscribed')
                    ->label(__('subscriber.is_subscribed'))
                    ->boolean(),

                TextColumn::make('email')
                    ->label(__('subscriber.email'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('period')
                    ->label(__('subscriber.period'))
                    ->sortable()
                    ->formatStateUsing(fn(Subscriber $subscriber): string => $subscriber->period->label()),

                TextColumn::make('subscribed_at')
                    ->label(__('subscriber.subscribed_at'))
                    ->sortable()
                    ->dateTime(get_datetime_format(), get_timezone()),

                TextColumn::make('updated_at')
                    ->label(__('ui.updated_at'))
                    ->sortable()
                    ->dateTime(get_datetime_format(), get_timezone()),
            ])
            ->filters([
                TernaryFilter::make('is_subscribed')
                    ->label(__('subscriber.status'))
                    ->trueLabel(__('subscriber.subscribed'))
                    ->falseLabel(__('subscriber.unsubscribed'))
                    ->queries(
                        true: fn(Builder $builder): Builder => $builder->subscribed(),
                        false: fn(Builder $builder): Builder => $builder->subscribed(false),
                        blank: fn(Builder $builder): Builder => $builder,
                    ),
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
            'index' => Pages\ListSubscribers::route('/'),
            'create' => Pages\CreateSubscriber::route('/create'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}
