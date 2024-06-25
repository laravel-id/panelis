<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

trait HasLocalTime
{
    private string $default = 'Y-m-d H:i:s';

    protected function localCreatedAt(): Attribute
    {
        return new Attribute(function (): Carbon {
            return $this->created_at
                ->timezone(get_timezone());
        });
    }

    protected function localUpdatedAt(): Attribute
    {
        return new Attribute(function (): Carbon {
            return $this->updated_at
                ->timezone(get_timezone());
        });
    }
}
