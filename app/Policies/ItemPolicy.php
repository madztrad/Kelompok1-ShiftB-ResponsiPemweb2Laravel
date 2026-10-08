<?php

namespace App\Policies;

use App\Enums\ModerationStatus;
use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    /** Admin bebas; pemilik hanya selama belum selesai & belum diblokir. */
    public function update(User $user, Item $item): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $item->user_id === $user->id
            && ! $item->isResolved()
            && $item->moderation_status !== ModerationStatus::Blocked;
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->isAdmin()
            || ($item->user_id === $user->id && ! $item->isResolved());
    }

    /** Pengguna tidak boleh mengklaim laporannya sendiri. */
    public function claim(User $user, Item $item): bool
    {
        return $item->user_id !== $user->id;
    }

    /** Daftar klaim hanya boleh dilihat pemilik laporan & admin. */
    public function viewClaims(User $user, Item $item): bool
    {
        return $user->isAdmin() || $item->user_id === $user->id;
    }
}