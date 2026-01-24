<?php

namespace App\Actions\Translation;

use Lorisleiva\Actions\Concerns\AsAction;

class MutateText
{
    use AsAction;

    public function handle(array $texts): array
    {
        return collect($texts)
            ->mapWithKeys(fn (array $text): array => [$text['lang'] => $text['line']])
            ->toArray();
    }
}
