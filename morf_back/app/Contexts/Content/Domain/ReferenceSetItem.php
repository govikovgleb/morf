<?php

declare(strict_types=1);

namespace App\Contexts\Content\Domain;

use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceSetItem extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'set_id',
        'reference_image_id',
    ];

    public function set()
    {
        return $this->belongsTo(ReferenceSet::class, 'set_id');
    }

    public function referenceImage()
    {
        return $this->belongsTo(ReferenceImage::class, 'reference_image_id');
    }
}
