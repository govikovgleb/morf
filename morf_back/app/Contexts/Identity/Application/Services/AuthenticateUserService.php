<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\Services;

use App\Contexts\Identity\Domain\User;

class AuthenticateUserService
{
    public function execute(string $token): ?User
    {
        $authHash = hash('sha256', $token);
        return User::where('auth_hash', $authHash)->first();
    }
}
