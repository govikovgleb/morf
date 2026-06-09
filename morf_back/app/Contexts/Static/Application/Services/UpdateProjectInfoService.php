<?php

declare(strict_types=1);

namespace App\Contexts\Static\Application\Services;

use App\Contexts\Static\Domain\ProjectInfo;
use Carbon\Carbon;

class UpdateProjectInfoService
{
    public function execute(string $key, mixed $value): ProjectInfo
    {
        $info = ProjectInfo::where('key', $key)->first();

        if ($info) {
            $info->update([
                'value' => $value,
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $info = ProjectInfo::create([
                'key' => $key,
                'value' => $value,
                'updated_at' => Carbon::now(),
            ]);
        }

        return $info;
    }
}
