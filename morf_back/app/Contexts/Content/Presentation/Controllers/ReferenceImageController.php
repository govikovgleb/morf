<?php

declare(strict_types=1);

namespace App\Contexts\Content\Presentation\Controllers;

use App\Contexts\Content\Domain\ReferenceImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Reference Images', description: 'Reference images for artwork creation')]
class ReferenceImageController
{
    #[OA\Get(
        path: '/reference-images',
        summary: 'List reference images',
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of images'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = ReferenceImage::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $images = $query->paginate(20);

        return response()->json($images);
    }
}
