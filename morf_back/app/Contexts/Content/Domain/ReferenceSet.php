<?php

declare(strict_types=1);

namespace App\Contexts\Content\Domain;

use App\Contexts\Identity\Domain\User;
use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferenceSet extends Model
{
    use HasFactory;
    use HasUuid;

    protected static string $factory = \Database\Factories\ReferenceSetFactory::class;

    protected $fillable = [
        'title',
        'week_start_date',
        'is_published',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'week_start_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ReferenceSetItem::class, 'set_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
