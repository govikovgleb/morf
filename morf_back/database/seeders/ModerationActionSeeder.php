<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Contexts\Artworks\Domain\Artwork;
use App\Contexts\Identity\Domain\User;
use App\Contexts\Moderation\Domain\ModerationAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ModerationActionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (! $admin) {
            return;
        }

        $pending = Artwork::where('status', 'pending')->get();
        $rejected = Artwork::where('status', 'rejected')->get();

        foreach ($pending as $artwork) {
            // Auto-approve some pending artworks
            if (rand(0, 1) === 1) {
                ModerationAction::create([
                    'id' => Str::uuid7()->toString(),
                    'target_type' => 'artwork',
                    'target_id' => $artwork->id,
                    'action' => 'approve',
                    'actor_id' => $admin->id,
                    'reason' => null,
                ]);
            }
        }

        foreach ($rejected as $artwork) {
            ModerationAction::create([
                'id' => Str::uuid7()->toString(),
                'target_type' => 'artwork',
                'target_id' => $artwork->id,
                'action' => 'reject',
                'actor_id' => $admin->id,
                'reason' => 'Does not meet community guidelines.',
            ]);
        }
    }
}
