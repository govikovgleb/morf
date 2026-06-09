<?php

declare(strict_types=1);

namespace App\Contexts\Engagement\Presentation\Controllers;

use App\Contexts\Engagement\Application\Services\ToggleLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Engagement', description: 'Likes and comments')]
class LikeController
{
    public function __construct(
        private readonly ToggleLikeService $toggleService,
    ) {}

    #[OA\Post(
        path: '/artworks/{artwork_id}/likes',
        summary: 'Toggle like on an artwork',
        security: [['deviceToken' => []]],
        parameters: [
            new OA\Parameter(name: 'artwork_id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Like toggled',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'liked', type: 'boolean'),
                        new OA\Property(property: 'likes_count', type: 'integer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function toggle(Request $request, string $artworkId): JsonResponse
    {
        $userId = $request->attributes->get('auth_user_id');

        $result = $this->toggleService->execute($artworkId, $userId);

        return response()->json($result);
    }
}
