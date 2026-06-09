<?php

declare(strict_types=1);

namespace App\Contexts\Engagement\Domain;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Identity\Domain\User;
use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'artwork_id',
        'user_id',
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class, 'artwork_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
