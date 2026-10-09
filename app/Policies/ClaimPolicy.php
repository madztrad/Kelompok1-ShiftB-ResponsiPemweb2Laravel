<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{
    /** Yang memutuskan klaim: pemilik laporan atau admin. */
    public function review(User $user, Claim $claim): bool
    {
        return $user->isAdmin() || $claim->item->user_id === $user->id;
    }
}
