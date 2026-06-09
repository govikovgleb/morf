<?php

declare(strict_types=1);

namespace App\Contexts\Static\Presentation\Controllers;

use App\Contexts\Static\Application\Services\GetProjectInfoService;
use App\Contexts\Static\Application\Services\UpdateProjectInfoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Project Info', description: 'Static project information')]
class ProjectInfoController
{
    public function __construct(
        private readonly GetProjectInfoService $getService,
        private readonly UpdateProjectInfoService $updateService,
    ) {}

    #[OA\Get(
        path: '/project-info/{key}',
        summary: 'Get project info by key',
        parameters: [
            new OA\Parameter(name: 'key', in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'welcome_text'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Project info'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(string $key): JsonResponse
    {
        $info = $this->getService->execute($key);

        if (!$info) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($info);
    }

    #[OA\Put(
        path: '/admin/project-info/{key}',
        summary: 'Update project info (admin only)',
        security: [['deviceToken' => []]],
        parameters: [
            new OA\Parameter(name: 'key', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['value'],
                properties: [
                    new OA\Property(property: 'value', type: 'object'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Updated'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden (not admin)'),
        ]
    )]
    public function update(Request $request, string $key): JsonResponse
    {
        $validated = $request->validate([
            'value' => 'required',
        ]);

        $info = $this->updateService->execute($key, $validated['value']);

        return response()->json($info);
    }
}
