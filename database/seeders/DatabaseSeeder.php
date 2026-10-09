<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Akun pengujian (password semuanya: "password"):
     *  - admin@lostfound.test  (admin)
     *  - user@lostfound.test   (user)
     *  - siti@lostfound.test   (user)
     */
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        User::factory()->admin()->create([
            'name' => 'Admin Lost Found',
            'email' => 'admin@lostfound.test',
        ]);

        $users = collect([
            User::factory()->create(['name' => 'Budi Santoso', 'email' => 'user@lostfound.test']),
            User::factory()->create(['name' => 'Siti Aminah', 'email' => 'siti@lostfound.test']),
            User::factory()->create(['name' => 'Rina Kusuma', 'email' => 'rina@lostfound.test']),
        ]);

        $categoryIds = Category::pluck('id');

        $make = fn (string $state, int $count) => Item::factory()
            ->count($count)
            ->{$state}()
            ->state(fn () => [
                'user_id' => $users->random()->id,
                'category_id' => $categoryIds->random(),
            ])
            ->create();

        $make('approved', 12);
        $make('pending', 5);
        $make('blocked', 2);
        $resolved = $make('resolved', 3);

        // klaim yang sudah diterima untuk barang yang selesai
        $resolved->each(function (Item $item) use ($users) {
            $claimer = $users->first(fn (User $u) => $u->id !== $item->user_id);
            Claim::factory()->accepted()->create(['item_id' => $item->id, 'user_id' => $claimer->id]);
        });

        // klaim yang masih menunggu untuk beberapa barang aktif
        Item::approved()->whereNull('resolved_at')->take(5)->get()->each(function (Item $item) use ($users) {
            $claimer = $users->first(fn (User $u) => $u->id !== $item->user_id);
            Claim::factory()->create(['item_id' => $item->id, 'user_id' => $claimer->id]);
        });
    }
}
