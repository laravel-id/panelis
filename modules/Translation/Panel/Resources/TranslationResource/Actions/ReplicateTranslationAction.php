<?php

namespace Modules\Translation\Panel\Resources\TranslationResource\Actions;

use Filament\Actions\Action;
use Filament\Actions\ReplicateAction;
use Modules\Translation\Actions\MutateText;
use Modules\Translation\Panel\Resources\TranslationResource\Enums\TranslationPermission;
use Modules\Translation\Panel\Resources\TranslationResource\Forms\TranslationForm;

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
