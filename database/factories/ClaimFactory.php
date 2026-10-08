<?php

namespace Database\Factories;

use App\Enums\ClaimStatus;
use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Claim>
 */
class ClaimFactory extends Factory
{
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'user_id' => User::factory(),
            'message' => fake()->randomElement([
                'Ini barang saya, ada stiker kecil di bagian belakang sebagai ciri khusus.',
                'Saya kehilangan barang ini kemarin, bisa menyebutkan isi di dalamnya.',
                'Barang ini milik saya, saya punya foto lama yang menunjukkan barang tersebut.',
            ]),
            'status' => ClaimStatus::Pending,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => ClaimStatus::Accepted,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => ClaimStatus::Rejected,
            'reviewed_at' => now(),
        ]);
    }
}