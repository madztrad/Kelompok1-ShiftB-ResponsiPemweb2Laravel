<?php

namespace Database\Factories;

use App\Enums\ItemType;
use App\Enums\ModerationStatus;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        $titles = [
            'Dompet kulit warna hitam', 'Kunci motor Honda', 'HP Samsung Galaxy A54',
            'KTM atas nama mahasiswa', 'Tas ransel biru navy', 'Botol minum stainless',
            'Payung lipat hitam', 'Flashdisk 32GB', 'Jaket denim', 'Kacamata minus bingkai hitam',
            'Charger laptop ASUS', 'Earphone bluetooth putih',
        ];
        $locations = [
            'Perpustakaan pusat', 'Kantin gedung A', 'Parkiran motor', 'Masjid kampus',
            'Laboratorium komputer', 'Taman depan rektorat', 'Ruang kuliah B2.3', 'Lapangan basket',
        ];
        $descriptions = [
            'Ditemukan tergeletak di dekat tempat duduk, kondisi masih baik.',
            'Hilang kemarin sore, mohon info jika ada yang menemukan.',
            'Ada gantungan kunci kecil berwarna merah sebagai ciri khusus.',
            'Bagian dalam berisi beberapa kartu, harap hubungi untuk verifikasi.',
        ];

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => fake()->randomElement($titles),
            'description' => fake()->randomElement($descriptions),
            'type' => fake()->randomElement(ItemType::cases()),
            'location' => fake()->randomElement($locations),
            'event_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'photo_path' => null,
            'moderation_status' => ModerationStatus::Pending,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['moderation_status' => ModerationStatus::Pending]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'moderation_status' => ModerationStatus::Approved,
            'moderated_at' => now(),
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn () => [
            'moderation_status' => ModerationStatus::Blocked,
            'blocked_reason' => 'Konten tidak sesuai ketentuan.',
            'moderated_at' => now(),
        ]);
    }

    public function resolved(): static
    {
        return $this->approved()->state(fn () => ['resolved_at' => now()]);
    }
}
