<?php

declare(strict_types=1);

namespace App\Contexts\Presentation\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Morf API',
    version: '1.0.0',
    description: 'API for the Morf community — weekly reference sets, artwork uploads, likes, and moderation.'
)]
#[OA\Server(url: '/api', description: 'Local development server')]
#[OA\SecurityScheme(
    securityScheme: 'deviceToken',
    type: 'apiKey',
    in: 'header',
    name: 'X-Device-Token',
    description: 'Anonymous device token for authentication'
)]
class OpenApiSpec
{
}
