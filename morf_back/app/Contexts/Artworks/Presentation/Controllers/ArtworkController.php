<?php

declare(strict_types=1);

namespace App\Contexts\Artworks\Presentation\Controllers;

use App\Contexts\Artworks\Application\Dto\UploadArtworkDto;
use App\Contexts\Artworks\Application\Services\SoftDeleteArtworkService;
use App\Contexts\Artworks\Application\Services\UploadArtworkService;
use App\Contexts\Artworks\Domain\Artwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Artworks', description: 'Artwork upload, feed, and moderation')]
class ArtworkController
{
    public function __construct(
        private readonly UploadArtworkService $uploadService,
        private readonly SoftDeleteArtworkService $deleteService,
    ) {}

    #[OA\Get(
        path: '/artworks',
        summary: 'Global artwork feed (approved only)',
        parameters: [
            new OA\Parameter(name: 'reference_set_id', in: 'query', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated artworks'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Artwork::approved();

        if ($request->has('reference_set_id')) {
            $query->where('reference_set_id', $request->reference_set_id);
        }

        $artworks = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($artworks);
    }

    #[OA\Get(
        path: '/artworks/{id}',
        summary: 'Get single approved artwork',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Artwork details'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $artwork = Artwork::approved()->findOrFail($id);

        return response()->json($artwork);
    }

    #[OA\Post(
        path: '/artworks',
        summary: 'Upload a new artwork',
        security: [['deviceToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['reference_set_id', 'image'],
                    properties: [
                        new OA\Property(property: 'reference_set_id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary'),
                        new OA\Property(property: 'caption', type: 'string', maxLength: 1000),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Artwork created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference_set_id' => 'required|string',
            'image' => 'required|image|max:20480',
            'caption' => 'nullable|string|max:1000',
        ]);

        $dto = new UploadArtworkDto(
            userId: $request->attributes->get('auth_user_id'),
            referenceSetId: $validated['reference_set_id'],
            file: $request->file('image'),
            caption: $validated['caption'] ?? null,
        );

        $artwork = $this->uploadService->execute($dto);

        return response()->json($artwork, 201);
    }

    #[OA\Delete(
        path: '/artworks/{id}',
        summary: 'Soft delete an artwork',
        security: [['deviceToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->deleteService->execute($id);

        return response()->json(['message' => 'Artwork deleted'], 204);
    }
}
