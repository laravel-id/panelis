<?php

namespace App\Actions\Translation;

use App\Models\Translation;
use Lorisleiva\Actions\Concerns\AsAction;

class Import
{
    use AsAction;

    public function handle(array $lines, string $locale): void
    {
        foreach ($lines as $index => $line) {
            [$group, $key] = explode('.', $index, 2);

            $trans = Translation::query()
                ->firstOrNew([
                    'group' => $group,
                    'key' => $key,
                ]);

            $newLine = [$locale => $line['text']];

            $trans->is_system = $line['is_system'];
            if (! empty($trans->text)) {
                $trans->text = array_merge($trans->text, $newLine);
            } else {
                $trans->text = $newLine;
            }

            $trans->save();
        }
    }
}
