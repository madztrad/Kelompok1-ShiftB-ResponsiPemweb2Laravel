<?php

namespace App\Http\Resources;

use App\Models\Item;
use App\Services\ItemPhotoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Item */
class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $viewer = $request->user('sanctum');
        $isOwnerOrAdmin = $viewer && ($viewer->isAdmin() || $viewer->id === $this->user_id);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,                       // lost | found
            'location' => $this->location,
            'event_date' => $this->event_date?->toDateString(),
            'photo_url' => app(ItemPhotoService::class)->url($this->photo_path),

            'moderation_status' => $this->moderation_status, // pending | approved | blocked
            'is_resolved' => $this->isResolved(),
            'resolved_at' => $this->resolved_at?->toIso8601String(),

            'category' => $this->whenLoaded('category', fn () => CategoryResource::make($this->category)),
            'reporter' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'claims_count' => $this->whenCounted('claims'),

            // hanya untuk pemilik laporan & admin
            'blocked_reason' => $this->when($isOwnerOrAdmin, $this->blocked_reason),
            'moderated_at' => $this->when($isOwnerOrAdmin, fn () => $this->moderated_at?->toIso8601String()),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
