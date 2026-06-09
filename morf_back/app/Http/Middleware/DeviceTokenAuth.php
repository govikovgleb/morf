<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Contexts\Identity\Application\Services\AuthenticateUserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceTokenAuth
{
    public function __construct(
        private readonly AuthenticateUserService $authService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Device-Token');

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $this->authService->execute($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->attributes->set('auth_user_id', $user->id);
        $request->attributes->set('auth_user', $user);

        return $next($request);
    }
}
