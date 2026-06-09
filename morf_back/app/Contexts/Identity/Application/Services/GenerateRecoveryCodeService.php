<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\Services;

use App\Contexts\Identity\Domain\User;

class GenerateRecoveryCodeService
{
    public function execute(User $user): string
    {
        $code = strtoupper(bin2hex(random_bytes(6))); // 12 символов
        $hash = hash('sha256', $code);

        $user->update(['recovery_code_hash' => $hash]);

        return $code;
    }
}
