<?php

namespace App\Filament\Resources\TranslationResource\Actions;

use App\Actions\Translation\MutateText;
use App\Filament\Resources\TranslationResource\Enums\TranslationPermission;
use App\Filament\Resources\TranslationResource\Forms\TranslationForm;
use Filament\Actions\Action;
use Filament\Actions\ReplicateAction;

class ReplicateTranslationAction
{
    public static function make(string $name = 'replicate_action'): Action
    {
        return ReplicateAction::make($name)
            ->visible(user_can(TranslationPermission::Add))
            ->mutateDataUsing(function (array $data): array {
                $data['text'] = MutateText::run($data['text']);

                return $data;
            })
            ->schema(TranslationForm::schema());
    }
}
