<?php

namespace App\Filament\Resources\Blog\CategoryResource\Pages;

use App\Filament\Resources\Blog\CategoryResource;
use Filament\Actions;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->visible(Auth::user()->can('View blog category')),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(config('modules.blog'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('View blog category'), Response::HTTP_FORBIDDEN);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(3)
            ->schema([
                Components\Section::make()
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Components\TextEntry::make('name')
                            ->size(Components\TextEntry\TextEntrySize::Large),

                        Components\TextEntry::make('slug')
                            ->size(Components\TextEntry\TextEntrySize::Large),

                        Components\TextEntry::make('description')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),

                Components\Section::make()
                    ->columnSpan(1)
                    ->schema([
                        Components\TextEntry::make('created_at')
                            ->translateLabel(),

                        Components\TextEntry::make('updated_at')
                            ->translateLabel(),

                        Components\TextEntry::make('deleted_at')
                            ->hidden(fn (?Model $record): bool => empty($record->deleted_at))
                            ->translateLabel(),
                    ]),
            ]);
    }
}
