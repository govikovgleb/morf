<?php

namespace App\Providers;

use App\Contexts\Artworks\Application\Listeners\LogArtworkSubmission;
use App\Contexts\Artworks\Application\Listeners\LogModerationDecision;
use App\Contexts\Artworks\Domain\Events\ArtworkModerated;
use App\Contexts\Artworks\Domain\Events\ArtworkSubmitted;
use App\Contexts\Content\Application\Listeners\ClearPublishedSetsCache;
use App\Contexts\Content\Domain\Events\ReferenceSetPublished;
use App\Contexts\Engagement\Application\Listeners\LogLikeActivity;
use App\Contexts\Engagement\Domain\Events\LikeToggled;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ArtworkSubmitted::class => [
            LogArtworkSubmission::class,
        ],
        ArtworkModerated::class => [
            LogModerationDecision::class,
        ],
        LikeToggled::class => [
            LogLikeActivity::class,
        ],
        ReferenceSetPublished::class => [
            ClearPublishedSetsCache::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
