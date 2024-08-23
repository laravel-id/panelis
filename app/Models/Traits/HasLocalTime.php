<?php

namespace App\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasLocalTime
{
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

    public function localDeletedAt(): Attribute
    {
        return new Attribute(function (): ?Carbon {
            if (! empty($this->deleted_at)) {
                return $this->deleted_at
                    ->timezone(get_timezone());
            }

            return null;
        });
    }
}
