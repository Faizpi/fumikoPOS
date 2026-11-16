<?php

namespace App\Models\Concerns;

trait UsesUuidRouteKey
{
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field !== null) {
            return $this->where($field, $value)->firstOrFail();
        }

        return $this->where('uuid', $value)
            ->when(is_numeric($value), fn ($q) => $q->orWhere('id', (int) $value))
            ->firstOrFail();
    }
}
