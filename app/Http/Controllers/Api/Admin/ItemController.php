<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\ModerationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\ItemFilterRequest;
use App\Http\Requests\Item\ModerateItemRequest;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Services\ItemPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class ItemController extends Controller
{
    /** Admin: semua laporan tanpa memandang status moderasi. */
    public function index(ItemFilterRequest $request): AnonymousResourceCollection
    {
        $items = Item::query()
            ->with(['category', 'user:id,name'])
            ->withCount('claims')
            ->filter($request->filters())
            ->latest('id')
            ->paginate($request->perPage());

        return ItemResource::collection($items);
    }

    public function show(Item $item): ItemResource
    {
        return ItemResource::make($item->load(['category', 'user:id,name'])->loadCount('claims'));
    }

    /** Laporan yang dibuat admin langsung disetujui. */
    public function store(StoreItemRequest $request, ItemPhotoService $photos): JsonResponse
    {
        $admin = $request->user();

        $item = $admin->items()->create([
            ...Arr::except($request->validated(), ['photo']),
            'photo_path' => $photos->store($request->file('photo')),
            'moderation_status' => ModerationStatus::Approved,
            'moderated_by' => $admin->id,
            'moderated_at' => now(),
        ]);

        return ItemResource::make($item->load(['category', 'user:id,name']))
            ->response()
            ->setStatusCode(201);
    }

    /** Admin bebas mengubah laporan apa pun; status moderasi tidak diubah. */
    public function update(UpdateItemRequest $request, Item $item, ItemPhotoService $photos): ItemResource
    {
        Gate::authorize('update', $item);

        $data = Arr::except($request->validated(), ['photo']);

        if ($request->hasFile('photo')) {
            $photos->delete($item->photo_path);
            $data['photo_path'] = $photos->store($request->file('photo'));
        }

        $item->update($data);

        return ItemResource::make($item->load(['category', 'user:id,name']));
    }

    public function destroy(Item $item, ItemPhotoService $photos): Response
    {
        Gate::authorize('delete', $item);

        $photos->delete($item->photo_path);
        $item->delete();

        return response()->noContent();
    }

    /** Setujui atau blokir laporan (blokir wajib disertai alasan). */
    public function moderate(ModerateItemRequest $request, Item $item): ItemResource
    {
        $status = ModerationStatus::from($request->validated('status'));

        $item->update([
            'moderation_status' => $status,
            'blocked_reason' => $status === ModerationStatus::Blocked
                ? $request->validated('reason')
                : null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return ItemResource::make($item->load(['category', 'user:id,name']));
    }
}
