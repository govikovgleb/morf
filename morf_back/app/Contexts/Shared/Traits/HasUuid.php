<?php

declare(strict_types=1);

namespace App\Contexts\Shared\Traits;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait HasUuid
{
    public function initializeHasUuid(): void
    {
        $this->setKeyType('string');
        $this->setIncrementing(false);
    }

    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->getKey())) {
                $model->setAttribute($model->getKeyName(), Uuid::uuid7()->toString());
            }
        });
    }
}
