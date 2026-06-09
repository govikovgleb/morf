<?php

declare(strict_types=1);

namespace App\Contexts\Moderation\Domain;

use App\Contexts\Identity\Domain\User;
use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationAction extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'target_type',
        'target_id',
        'action',
        'actor_id',
        'reason',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
