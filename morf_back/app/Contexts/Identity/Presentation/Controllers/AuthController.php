<?php

declare(strict_types=1);

namespace App\Contexts\Identity\Presentation\Controllers;

use App\Contexts\Identity\Application\Dto\RegisterAnonymousUserDto;
use App\Contexts\Identity\Application\Services\AuthenticateUserService;
use App\Contexts\Identity\Application\Services\RecoverAccountService;
use App\Contexts\Identity\Application\Services\RegisterAnonymousUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth', description: 'Anonymous user registration and recovery')]
class AuthController
{
    public function __construct(
        private readonly RegisterAnonymousUserService $registerService,
        private readonly AuthenticateUserService $authService,
        private readonly RecoverAccountService $recoverService,
    ) {}

    #[OA\Post(
        path: '/auth/register',
        summary: 'Register an anonymous user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nickname'],
                properties: [
                    new OA\Property(property: 'nickname', type: 'string', minLength: 3, maxLength: 50, example: 'artist_01'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created', content: new OA\JsonContent(properties: [new OA\Property(property: 'token', type: 'string')])),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nickname' => 'required|string|min:3|max:50',
        ]);

        $dto = new RegisterAnonymousUserDto($validated['nickname']);
        $token = $this->registerService->execute($dto);

        return response()->json(['token' => $token], 201);
    }

    #[OA\Post(
        path: '/auth/recover',
        summary: 'Recover account by recovery code',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['recovery_code'],
                properties: [
                    new OA\Property(property: 'recovery_code', type: 'string', minLength: 12, maxLength: 12, example: 'ABC123DEF456'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'New token', content: new OA\JsonContent(properties: [new OA\Property(property: 'token', type: 'string')])),
            new OA\Response(response: 400, description: 'Invalid recovery code'),
        ]
    )]
    public function recover(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recovery_code' => 'required|string|size:12',
        ]);

        $token = $this->recoverService->execute($validated['recovery_code']);

        if (!$token) {
            return response()->json(['message' => 'Invalid recovery code'], 400);
        }

        return response()->json(['token' => $token]);
    }
}
