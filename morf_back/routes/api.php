<?php

use App\Contexts\Identity\Presentation\Controllers\AuthController;
use App\Contexts\Content\Presentation\Controllers\ReferenceSetController;
use App\Contexts\Content\Presentation\Controllers\ReferenceImageController;
use App\Contexts\Artworks\Presentation\Controllers\ArtworkController;
use App\Contexts\Engagement\Presentation\Controllers\LikeController;
use App\Contexts\Moderation\Presentation\Controllers\ModerationController;
use App\Contexts\Static\Presentation\Controllers\ProjectInfoController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/recover', [AuthController::class, 'recover']);

Route::get('/reference-sets', [ReferenceSetController::class, 'index']);
Route::get('/reference-sets/{id}', [ReferenceSetController::class, 'show']);
Route::get('/reference-images', [ReferenceImageController::class, 'index']);

Route::get('/artworks', [ArtworkController::class, 'index']);
Route::get('/artworks/{id}', [ArtworkController::class, 'show']);

Route::get('/project-info/{key}', [ProjectInfoController::class, 'show']);

Route::middleware(['device.auth'])->group(function () {
    Route::post('/artworks', [ArtworkController::class, 'store']);
    Route::delete('/artworks/{id}', [ArtworkController::class, 'destroy']);

    Route::post('/artworks/{artwork_id}/likes', [LikeController::class, 'toggle']);
});

Route::middleware(['device.auth', 'admin'])->prefix('admin')->group(function () {
    Route::post('/artworks/{id}/approve', [ModerationController::class, 'approve']);
    Route::post('/artworks/{id}/reject', [ModerationController::class, 'reject']);
    Route::get('/moderation-actions', [ModerationController::class, 'index']);

    Route::put('/project-info/{key}', [ProjectInfoController::class, 'update']);
});
