<?php

declare(strict_types=1);

namespace App\Contexts\Content\Presentation\Controllers;

use App\Contexts\Content\Domain\ReferenceSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Reference Sets', description: 'Published weekly reference sets')]
class ReferenceSetController
{
    #[OA\Get(
        path: '/reference-sets',
        summary: 'List published reference sets',
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of sets'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $sets = ReferenceSet::with('items.referenceImage.category')
            ->where('is_published', true)
            ->orderBy('week_start_date', 'desc')
            ->paginate(10);

        return response()->json($sets);
    }

    #[OA\Get(
        path: '/reference-sets/{id}',
        summary: 'Get a single reference set with images',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reference set details'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $set = ReferenceSet::with('items.referenceImage.category')->findOrFail($id);

        return response()->json($set);
    }
}
