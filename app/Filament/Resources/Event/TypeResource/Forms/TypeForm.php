<?php

namespace App\Filament\Resources\Event\TypeResource\Forms;

use App\Models\Event\Type;
use Filament\Forms\Components\CheckboxList;

class TypeForm
{
    public static function schema(): array
    {
        return [
            CheckboxList::make('types')
                ->relationship()
                ->label(__('event.type'))
                ->searchable()
                ->bulkToggleable()
                ->options(Type::options()),
        ];
    }
}
