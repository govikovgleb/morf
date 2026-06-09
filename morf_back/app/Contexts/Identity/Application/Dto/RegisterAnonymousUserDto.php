<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Application\Dto;

class RegisterAnonymousUserDto
{
    public function __construct(
        public readonly string $nickname,
    ) {}
}
