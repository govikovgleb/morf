<?php

declare(strict_types=1);

namespace App\Contexts\Static\Domain;

use App\Contexts\Shared\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectInfo extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'project_info';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public $timestamps = false;
}
