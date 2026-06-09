<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\Services;

use App\Contexts\Identity\Domain\User;

class RecoverAccountService
{
    public function execute(string $code): ?string
    {
        $hash = hash('sha256', $code);
        $user = User::where('recovery_code_hash', $hash)->first();

        if (!$user) {
            return null;
        }

        $newToken = bin2hex(random_bytes(32));
        $newAuthHash = hash('sha256', $newToken);

        $user->update([
            'auth_hash' => $newAuthHash,
            'recovery_code_hash' => null,
        ]);

        return $newToken;
    }
}
