<?php

declare(strict_types=1);

namespace App\Contexts\Content\Domain;

use App\Contexts\Identity\Domain\User;
use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceImage extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'category_id',
        'cdn_url',
        'storage_path',
        'width',
        'height',
        'file_size_bytes',
        'mime_type',
        'uploaded_by',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'file_size_bytes' => 'integer',
    ];

    protected function cdnUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function (string $value) {
                if (str_starts_with($value, 'http')) {
                    return $value;
                }

                return rtrim(config('app.url'), '/').$value;
            },
        );
    }

    public function category()
    {
        return $this->belongsTo(ReferenceCategory::class, 'category_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
