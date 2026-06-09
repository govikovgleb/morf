<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Domain;

use App\Contexts\Content\Domain\ReferenceSet;
use App\Contexts\Identity\Domain\User;
use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artwork extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected static string $factory = \Database\Factories\ArtworkFactory::class;

    protected $fillable = [
        'user_id',
        'reference_set_id',
        'cdn_url',
        'storage_path',
        'width',
        'height',
        'file_size_bytes',
        'mime_type',
        'caption',
        'author_nickname',
        'status',
        'likes_count',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'file_size_bytes' => 'integer',
        'likes_count' => 'integer',
        'moderated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referenceSet()
    {
        return $this->belongsTo(ReferenceSet::class, 'reference_set_id');
    }
}
