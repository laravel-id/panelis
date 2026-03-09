<?php

namespace Modules\Branch\Panel\Resources\BranchResource\Forms;

use App\Filament\Pages\RegisterBranch;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Modules\Branch\Panel\Resources\BranchResource\Pages\EditBranch;

class BranchForm
{
    public static function schema(): array
    {
        return [
            Section::make([
                FileUpload::make('logo')
                    ->hiddenLabel()
                    ->avatar()
                    ->alignCenter()
                    ->disk('public')
                    ->directory('branches')
                    ->moveFiles()
                    ->image(),

                TextInput::make('name')
                    ->label(__('branch::branch.name'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, string $operation): void {
                        if (in_array($operation, [RegisterBranch::class, 'create'])) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->maxLength(100),

                TextInput::make('slug')
                    ->label(__('ui.slug'))
                    ->alphaDash()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('phone')
                    ->label(__('branch::branch.phone'))
                    ->nullable()
                    ->tel(),

                TextInput::make('email')
                    ->label(__('branch::branch.email'))
                    ->nullable()
                    ->placeholder('business@email-example.com')
                    ->email(),

                Textarea::make('address')
                    ->label(__('branch::branch.address'))
                    ->rows(5)
                    ->nullable(),

                KeyValue::make('metadata')
                    ->label(__('branch::branch.metadata'))
                    ->visibleOn(EditBranch::class),
            ])->columnSpanFull(),
        ];
    }
}
