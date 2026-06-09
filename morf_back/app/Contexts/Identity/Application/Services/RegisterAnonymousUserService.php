<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\Services;

use App\Contexts\Identity\Application\Dto\RegisterAnonymousUserDto;
use App\Contexts\Identity\Domain\User;

class RegisterAnonymousUserService
{
    public function execute(RegisterAnonymousUserDto $dto): string
    {
        $token = bin2hex(random_bytes(32));
        $authHash = hash('sha256', $token);

        User::create([
            'public_nickname' => $dto->nickname,
            'auth_hash' => $authHash,
        ]);

        return $token;
    }
}
