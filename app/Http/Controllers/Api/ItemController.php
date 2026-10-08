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

/** CRUD + moderasi semua laporan barang (khusus admin). */
class ItemController extends Controller
{
    /** Semua laporan, semua status. Filter utama: ?moderation_status=pending */
    public function index(ItemFilterRequest $request): AnonymousResourceCollection
    {
        $items = Item::with(['category', 'user:id,name'])
            ->withCount('claims')
            ->filter($request->filters())
            ->latest('id')
            ->paginate($request->perPage());

        return ItemResource::collection($items);
    }

    public function show(Item $item): ItemResource
    {
        return ItemResource::make($item->load(['category', 'user:id,name', 'moderator:id,name'])->loadCount('claims'));
    }

    /** Admin yang membuat laporan langsung berstatus approved. */
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

    /** Untuk upload foto saat update gunakan POST + field _method=PUT. */
    public function update(UpdateItemRequest $request, Item $item, ItemPhotoService $photos): ItemResource
    {
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
        $photos->delete($item->photo_path);
        $item->delete();

        return response()->noContent();
    }

    /** Setujui (approved) atau blokir (blocked, wajib alasan) sebuah laporan. */
    public function moderate(ModerateItemRequest $request, Item $item): ItemResource
    {
        $status = ModerationStatus::from($request->validated('status'));

        $item->update([
            'moderation_status' => $status,
            'blocked_reason' => $status === ModerationStatus::Blocked ? $request->validated('reason') : null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return ItemResource::make($item->load(['category', 'user:id,name']));
    }
}