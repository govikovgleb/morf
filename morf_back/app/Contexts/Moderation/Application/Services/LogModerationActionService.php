<?php

declare(strict_types=1);

namespace App\Contexts\Moderation\Application\Services;

use App\Contexts\Moderation\Domain\ModerationAction;

class LogModerationActionService
{
    public function execute(string $targetType, string $targetId, string $action, string $actorId, ?string $reason = null): ModerationAction
    {
        return ModerationAction::create([
            'target_type' => $targetType,
            'target_id' => $targetId,
            'action' => $action,
            'actor_id' => $actorId,
            'reason' => $reason,
        ]);
    }
}
