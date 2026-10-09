# Lost & Found — Sistem Informasi Barang Hilang & Temuan

> Platform pelaporan barang hilang (lost) & temuan (found) dengan moderasi admin, kategori, dan klaim kepemilikan.

---

## 📌 Informasi Kelompok

- **Nomor Kelompok:** [ Kelompok 01]
- **Shift Praktikum:** [ Shift B]

---

## 👥 Anggota Kelompok

| No  | Nama Lengkap          | NIM       | Shift Awal   | Shift Akhir   | Jobdesk / Kontribusi         | Link Video Penjelasan                   |
| --- | --------------------- | --------- | ------------ | ------------- | ---------------------------- | --------------------------------------- |
| 1   | Muhammad Faiz Mubarok | H1H024051 | Shift B      | Shift B       | CRUD Admin & Dashboard Admin | [YouTube](https://youtu.be/chOvOX0e-d8) |
| 2   | Hafish athallah       | H1H024052 | Shift D      | Shift B       | FE (Autentikasi & Page User) | [YouTube](https://youtu.be/nfqKGSokLC0?si=YxKUgO0hgN-uZNsm)            |
| 3   | Dimas Rafif Zaidan        | H1H024043     | [Shift Awal] | [Shift Akhir] | Backend              | [YouTube](https://youtu.be/bp65uXiZMds)            |

---

## 📖 Deskripsi Aplikasi

Aplikasi **Lost & Found** memudahkan pelaporan barang hilang dan temuan di lingkungan kampus/komunitas. Pengguna (user) dapat memposting laporan lengkap dengan foto, lokasi, tanggal kejadian, dan kategori, lalu pengguna lain dapat mengajukan **klaim** kepemilikan atas barang temuan.

**Tujuan:** menggantikan pengumuman manual (grup chat/mading) yang mudah tenggelam dengan satu katalog terpusat yang termoderasi.

**Target pengguna:**

- **Tamu:** browsing katalog barang & detail laporan yang sudah disetujui.
- **User login:** buat/kelola laporan miliknya, ajukan klaim, pantau status klaim.
- **Admin:** moderasi laporan (pending/approved/blocked), kelola item, user, dan kategori + dashboard ringkasan.

**Problem yang diselesaikan:** laporan duplikat/tidak valid (disaring via moderasi), klaim liar (diverifikasi pemilik lewat alur klaim), dan pencarian barang yang sulit (filter tipe, kategori, status).

---

## ⚙️ Penjelasan Teknis

### 1. Teknologi (Tech Stack)

- **Backend:** Laravel 13.x (PHP ^8.3), Livewire 4.x, Laravel Sanctum 4.x (token + abilities `user`/`admin`)
- **Frontend:** Blade + Livewire Component + Alpine.js (`x-data`, `x-init`) + Tailwind CSS 4.x + Vite Plus
- **Database:** SQLite (default pengembangan, `DB_CONNECTION=sqlite`), siap migrasi ke MySQL/PostgreSQL
- **Library / Package:** `livewire/blaze`, `laravel/tinker`, `laravel/boost`, `pestphp/pest` 5.x, `laravel/pint`, `larastan/larastan`

### 2. Fitur Utama & Modul

- **Autentikasi & Otorisasi:** Register/login via `POST /api/auth/*`, token Sanctum disimpan di session frontend; guard Livewire `AdminPage::mount()` (cek `session('api_token')` + `role === 'admin'`), middleware API `auth:sanctum` + `abilities:user/admin`, throttle login `10/menit`.
- **Modul Items (Laporan):** CRUD `POST/PUT/DELETE /api/items`, katalog publik `GET /api/items` + filter `moderation_status/type/category`, detail `GET /api/items/{id}`, halaman `items.index/create/show`, `my.items`, upload `photo_path`, status `resolved_at`.
- **Modul Claims (Klaim):** `POST /api/items/{id}/claims`, list klaim per item, `PATCH /api/claims/{id}` (approve/reject), halaman `my.claims`.
- **Modul Admin:** Dashboard ringkasan (`meta.total` per status), antrian moderasi `PATCH /api/admin/items/{id}/moderation` (approve/blocked + alasan), `apiResource` admin untuk `items/users/categories`, halaman `admin.index/moderation/items/users/categories`.

### 3. Skema Data Singkat

- `users` (1 : N) `items` — satu user bisa lapor banyak barang (`items.user_id`)
- `categories` (1 : N) `items` — satu kategori memuat banyak laporan (`items.category_id`)
- `items` (1 : N) `claims` — satu laporan bisa diklaim banyak user (`claims.item_id`)
- `users` (1 : N) `claims` — satu user bisa mengajukan banyak klaim (`claims.user_id`)
- `users` (1 : N) `claims` sebagai reviewer/moderator (`claims.reviewed_by`, `items.moderated_by`)

---

## 🚀 Panduan Instalasi Lokal

```bash
# Clone repository
git clone https://github.com/madztrad/Kelompok1-ShiftB-ResponsiPemweb2Laravel
cd Kelompok1-ShiftB-ResponsiPemweb2Laravel

# Install dependensi PHP & Node
composer install
npm install

# Konfigurasi Environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di file .env (default sqlite), lalu migrasi & seed
touch database/database.sqlite
php artisan migrate --seed

# Jalankan development server
composer run dev
# atau:
# php artisan serve
# npm run dev
```
