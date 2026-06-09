<?php

declare(strict_types=1);

namespace App\Contexts\Content\Domain;

use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceCategory extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
