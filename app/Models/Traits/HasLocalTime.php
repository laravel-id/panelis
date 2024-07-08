<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasLocalTime
{
    protected function localCreatedAt(): Attribute
    {
        return new Attribute(function (): string {
            return $this->created_at
                ->timezone(get_timezone())
                ->translatedFormat(get_datetime_format());
        });
    }

    protected function localUpdatedAt(): Attribute
    {
        return new Attribute(function (): string {
            return $this->updated_at
                ->timezone(get_timezone())
                ->translatedFormat(get_datetime_format());
        });
    }

    public function localDeletedAt(): Attribute
    {
        return new Attribute(function (): ?string {
            if (! empty($this->deleted_at)) {
                return $this->deleted_at
                    ->timezone(get_timezone())
                    ->translatedFormat(get_datetime_format());
            }

            return null;
        });
    }
}
