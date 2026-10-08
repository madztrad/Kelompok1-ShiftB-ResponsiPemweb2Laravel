<?php

namespace App\Http\Controllers\Api;

use App\Enums\ModerationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Item\ItemFilterRequest;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use App\Services\ItemPhotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class ItemController extends Controller
{
    /** Publik (beranda): hanya laporan yang sudah disetujui admin. */
    public function index(ItemFilterRequest $request): AnonymousResourceCollection
    {
        $items = Item::approved()
            ->with(['category', 'user:id,name'])
            ->filter($request->filters())
            ->latest('id')
            ->paginate($request->perPage());

        return ItemResource::collection($items);
    }

    /** Publik untuk laporan approved; pending/blocked hanya pemilik & admin. */
    public function show(Request $request, Item $item): ItemResource
    {
        $viewer = $request->user('sanctum');
        $canView = $item->isApproved()
            || ($viewer && ($viewer->isAdmin() || $viewer->id === $item->user_id));

        abort_unless($canView, 404);

        return ItemResource::make($item->load(['category', 'user:id,name'])->loadCount('claims'));
    }

    /** Laporan milik pengguna yang sedang login (semua status moderasi). */
    public function mine(ItemFilterRequest $request): AnonymousResourceCollection
    {
        $items = $request->user()->items()
            ->with('category')
            ->withCount('claims')
            ->filter($request->filters())
            ->latest('id')
            ->paginate($request->perPage());

        return ItemResource::collection($items);
    }

    /** Buat laporan baru → status moderasi otomatis "pending". */
    public function store(StoreItemRequest $request, ItemPhotoService $photos): JsonResponse
    {
        $item = $request->user()->items()->create([
            ...Arr::except($request->validated(), ['photo']),
            'photo_path' => $photos->store($request->file('photo')),
            'moderation_status' => ModerationStatus::Pending,
        ]);

        return ItemResource::make($item->load(['category', 'user:id,name']))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Pemilik mengubah laporannya. Jika laporan sudah approved, maka
     * kembali ke "pending" agar admin memeriksa ulang perubahannya.
     *
     * Catatan: untuk upload foto saat update gunakan POST + field _method=PUT.
     */
    public function update(UpdateItemRequest $request, Item $item, ItemPhotoService $photos): ItemResource
    {
        Gate::authorize('update', $item);

        $data = Arr::except($request->validated(), ['photo']);

        if ($request->hasFile('photo')) {
            $photos->delete($item->photo_path);
            $data['photo_path'] = $photos->store($request->file('photo'));
        }

        if ($item->isApproved()) {
            $data['moderation_status'] = ModerationStatus::Pending;
            $data['moderated_by'] = null;
            $data['moderated_at'] = null;
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
}
