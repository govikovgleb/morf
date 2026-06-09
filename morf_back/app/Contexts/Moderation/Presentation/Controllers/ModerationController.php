<?php

declare(strict_types=1);

namespace App\Contexts\Moderation\Presentation\Controllers;

use App\Contexts\Artworks\Application\Services\ModerateArtworkService;
use App\Contexts\Moderation\Application\Services\LogModerationActionService;
use App\Contexts\Moderation\Domain\ModerationAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Moderation', description: 'Admin moderation actions')]
class ModerationController
{
    public function __construct(
        private readonly ModerateArtworkService $moderateService,
        private readonly LogModerationActionService $logService,
    ) {}

    #[OA\Post(
        path: '/admin/artworks/{id}/approve',
        summary: 'Approve an artwork',
        security: [['deviceToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Artwork approved'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden (not admin)'),
        ]
    )]
    public function approve(Request $request, string $id): JsonResponse
    {
        $actorId = $request->attributes->get('auth_user_id');

        $artwork = $this->moderateService->execute($id, 'approve', $actorId);

        $this->logService->execute('artwork', $id, 'approve', $actorId);

        return response()->json($artwork);
    }

    #[OA\Post(
        path: '/admin/artworks/{id}/reject',
        summary: 'Reject an artwork',
        security: [['deviceToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'reason', type: 'string', maxLength: 1000),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Artwork rejected'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden (not admin)'),
        ]
    )]
    public function reject(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $actorId = $request->attributes->get('auth_user_id');

        $artwork = $this->moderateService->execute($id, 'reject', $actorId, $validated['reason'] ?? null);

        $this->logService->execute('artwork', $id, 'reject', $actorId, $validated['reason'] ?? null);

        return response()->json($artwork);
    }

    #[OA\Get(
        path: '/admin/moderation-actions',
        summary: 'List moderation actions log',
        security: [['deviceToken' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Paginated moderation log'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden (not admin)'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $actions = ModerationAction::with('actor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($actions);
    }
}
