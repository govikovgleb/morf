<?php

declare(strict_types=1);

namespace App\Contexts\Static\Application\Services;

use App\Contexts\Static\Domain\ProjectInfo;

class GetProjectInfoService
{
    public function execute(string $key): ?ProjectInfo
    {
        return ProjectInfo::where('key', $key)->first();
    }
}
