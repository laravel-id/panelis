<?php

namespace App\Filament\Widgets\SimpleAnalytics;

use App\Models\SimpleAnalytics\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TopPage extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()->can('SeeTopPageWidget');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Page::query())
            ->columns([
                Tables\Columns\TextColumn::make('value')
                    ->label(__('widget.sa_url'))
                    ->url(fn (string $state): string => url($state))
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('pageviews')
                    ->label(__('widget.sa_page_views')),

                Tables\Columns\TextColumn::make('visitors')
                    ->label(__('widget.sa_visitors')),
            ]);
    }
}
