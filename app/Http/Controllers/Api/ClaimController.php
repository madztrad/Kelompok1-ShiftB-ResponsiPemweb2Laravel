<?php

namespace App\Http\Controllers\Api;

use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Claim\ReviewClaimRequest;
use App\Http\Requests\Claim\StoreClaimRequest;
use App\Http\Resources\ClaimResource;
use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ClaimController extends Controller
{
    /** Pemilik laporan / admin melihat klaim yang masuk pada sebuah barang. */
    public function indexForItem(Request $request, Item $item): AnonymousResourceCollection
    {
        Gate::authorize('viewClaims', $item);

        $claims = $item->claims()
            ->with('user')
            ->latest('id')
            ->paginate($this->perPage($request));

        return ClaimResource::collection($claims);
    }

    /** Klaim yang pernah diajukan oleh pengguna yang sedang login. */
    public function mine(Request $request): AnonymousResourceCollection
    {
        $claims = $request->user()->claims()
            ->with('item.category')
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest('id')
            ->paginate($this->perPage($request));

        return ClaimResource::collection($claims);
    }

    /** Ajukan klaim atas barang (status awal: pending). */
    public function store(StoreClaimRequest $request, Item $item): JsonResponse
    {
        Gate::authorize('claim', $item);

        abort_unless($item->isApproved(), 404);

        if ($item->isResolved()) {
            abort(409, 'Barang ini sudah dikembalikan, klaim tidak dapat diajukan.');
        }

        $alreadyPending = $item->claims()
            ->where('user_id', $request->user()->id)
            ->where('status', ClaimStatus::Pending->value)
            ->exists();

        if ($alreadyPending) {
            abort(409, 'Anda sudah memiliki klaim yang menunggu untuk barang ini.');
        }

        $claim = $item->claims()->create([
            'user_id' => $request->user()->id,
            'message' => $request->validated('message'),
            'status' => ClaimStatus::Pending,
        ]);

        return ClaimResource::make($claim->load('user'))->response()->setStatusCode(201);
    }

    /**
     * Terima / tolak klaim.
     * Jika diterima: barang ditandai selesai (resolved) dan klaim pending
     * lain untuk barang yang sama otomatis ditolak.
     */
    public function update(ReviewClaimRequest $request, Claim $claim): ClaimResource
    {
        Gate::authorize('review', $claim);

        if ($claim->status !== ClaimStatus::Pending) {
            abort(409, 'Klaim ini sudah diproses.');
        }

        $item = $claim->item;

        if ($item->isResolved()) {
            abort(409, 'Barang ini sudah dikembalikan.');
        }

        $status = ClaimStatus::from($request->validated('status'));
        $reviewerId = $request->user()->id;

        DB::transaction(function () use ($claim, $item, $status, $reviewerId) {
            $claim->update([
                'status' => $status,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
            ]);

            if ($status === ClaimStatus::Accepted) {
                $item->update(['resolved_at' => now()]);

                Claim::where('item_id', $item->id)
                    ->where('id', '!=', $claim->id)
                    ->where('status', ClaimStatus::Pending->value)
                    ->update([
                        'status' => ClaimStatus::Rejected->value,
                        'reviewed_by' => $reviewerId,
                        'reviewed_at' => now(),
                    ]);
            }
        });

        return ClaimResource::make($claim->fresh(['user', 'item']));
    }

    private function perPage(Request $request): int
    {
        return max(1, min($request->integer('per_page', 10), 50));
    }
}
