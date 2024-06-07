<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasLocalTime
{
    private string $default = 'Y-m-d H:i:s';

    protected function localCreatedAt(): Attribute
    {
        return new Attribute(function (): string {
            return $this->created_at
                ->timezone(config('app.datetime_timezone'))
                ->translatedFormat(config('app.datetime_format', $this->default));
        });
    }

    protected function localUpdatedAt(): Attribute
    {
        return new Attribute(function (): string {
            return $this->updated_at
                ->timezone(config('app.datetime_timezone'))
                ->translatedFormat(config('app.datetime_format', $this->default));
        });
    }
}
