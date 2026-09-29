<div align="center">

# Praktikum Pemrograman Web 2

### Aplikasi Laravel + Livewire

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Status](https://img.shields.io/badge/status-in%20development-yellow?style=for-the-badge)]()

</div>

---

## Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Tim & Pembagian Tugas](#-tim--pembagian-tugas)
- [Tech Stack](#-tech-stack)
- [Cara Menjalankan Proyek](#-cara-menjalankan-proyek)
- [Struktur Proyek](#-struktur-proyek)
- [Alur Kerja Tim](#-alur-kerja-tim)
- [Checklist Fitur](#-checklist-fitur)
- [Perintah Penting](#-perintah-penting)
- [Aturan Kontribusi](#-aturan-kontribusi)

---

## Tentang Proyek

Proyek ini dibuat untuk memenuhi tugas **Praktikum Pemrograman Web 2**. Aplikasi dibangun
menggunakan **Laravel** sebagai framework backend dan **Livewire** sebagai teknologi
frontend reaktif, sehingga antarmuka dapat berinteraksi secara dinamis tanpa menulis
banyak JavaScript.

> **Status:** Proyek masih tahap awal (starter kit). Fitur akan ditambahkan secara bertahap
> sesuai pembagian peran di bawah.

<details>
<summary><b>Klik untuk melihat tujuan pembelajaran</b></summary>

<br>

- Memahami konsep MVC (Model-View-Controller) pada Laravel.
- Membangun komponen antarmuka reaktif dengan Livewire.
- Menerapkan migrasi database dan Eloquent ORM.
- Berkolaborasi menggunakan Git dengan pembagian peran yang jelas.
- Menyusun tampilan responsif dengan Tailwind CSS.

</details>

---

## 👥 Tim & Pembagian Tugas

| Peran                     | Nama      | Tanggung Jawab Utama                                                             |
| :------------------------ | :-------- | :------------------------------------------------------------------------------- |
| 🎯 **Project Manager**    | **Faiz**  | Mengatur timeline, koordinasi tim, review hasil kerja, memastikan target selesai |
| ⚙️ **Backend Developer**  | **Dimas** | Model, migrasi database, controller, logika Livewire, validasi & keamanan        |
| 🎨 **Frontend Developer** | **Rasta** | Tampilan Blade, komponen Livewire, styling Tailwind CSS, layout responsif        |
| 🎨 **Frontend Developer** | **Hafiz** | Tampilan Blade, komponen Livewire, styling Tailwind CSS, interaksi UI/UX         |

<details>
<summary><b>Klik untuk melihat rincian tanggung jawab tiap peran</b></summary>

<br>

**🎯 Project Manager**

- Menyusun rencana kerja dan target setiap tahap.
- Membagi tugas dan memastikan tidak ada pekerjaan yang tumpang tindih.
- Melakukan review sebelum kode di-merge ke `main`.
- Menjadi penghubung komunikasi antar anggota tim.

**⚙️ Backend Developer (Dimas)**

- Merancang struktur database (migrasi, relasi, seeder).
- Membuat Model dan relasi Eloquent.
- Menulis logika pada komponen Livewire dan controller.
- Menangani validasi input, autentikasi, dan keamanan data.

**🎨 Frontend Developer (Rasta & Hafiz)**

- Menyusun layout dan komponen tampilan menggunakan Blade.
- Menghubungkan UI dengan state Livewire (`wire:model`, `wire:click`, dll).
- Styling dengan Tailwind CSS agar tampilan rapi dan responsif.
- Menjaga konsistensi desain dan pengalaman pengguna.

</details>

---

## 🛠 Tech Stack

<table>
<tr>
<td><b>Backend</b></td>
<td>

- PHP 8.3+
- Laravel 13.x
- Laravel Tinker

</td>
</tr>
<tr>
<td><b>Frontend</b></td>
<td>

- Livewire 4.x
- Tailwind CSS 4.x
- Vite 8.x

</td>
</tr>
<tr>
<td><b>Database</b></td>
<td>

- SQLite (default pengembangan)

</td>
</tr>
<tr>
<td><b>Pengujian & Kualitas</b></td>
<td>

- Pest PHP 5.x
- Laravel Pint (formatting)
- Larastan / PHPStan (static analysis)

</td>
</tr>
</table>

---

## 🚀 Cara Menjalankan Proyek

<details open>
<summary><b>Prasyarat</b></summary>

<br>

Pastikan sudah terpasang:

- PHP >= 8.3
- Composer
- Node.js & NPM
- Git

</details>

<details open>
<summary><b>Langkah Instalasi</b></summary>

<br>

```bash
# 1. Clone repositori
git clone <url-repositori>
cd laravel-prak-pemweb2

# 2. Jalankan setup otomatis (install, .env, key, migrate, build)
composer run setup

# 3. Jalankan server pengembangan
composer run dev
```

Buka aplikasi di browser pada alamat yang ditampilkan di terminal
(biasanya `http://127.0.0.1:8000`).

</details>

<details>
<summary><b>Setup Manual (jika <code>composer run setup</code> gagal)</b></summary>

<br>

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
php artisan serve
```

</details>

---

## 📁 Struktur Proyek

<details>
<summary><b>Klik untuk melihat struktur folder</b></summary>

<br>

```text
laravel-prak-pemweb2/
├── app/
│   ├── Http/Controllers/     # Controller aplikasi
│   ├── Models/               # Model Eloquent (User, dll)
│   └── Providers/            # Service provider
├── database/
│   ├── migrations/           # Skema database
│   ├── factories/            # Data dummy untuk testing
│   └── seeders/              # Pengisian data awal
├── resources/
│   ├── css/                  # Sumber Tailwind CSS
│   ├── js/                   # Sumber JavaScript
│   └── views/                # Template Blade
├── routes/
│   ├── web.php               # Rute web
│   └── console.php           # Rute console
├── tests/                    # Pengujian Pest
├── AGENTS.md                 # Panduan pengembangan
└── README.md                 # Dokumen ini
```

</details>

---

## 🔀 Alur Kerja Tim

<details>
<summary><b>Klik untuk melihat aturan Git & kolaborasi</b></summary>

<br>

**Penamaan branch**

```text
fitur/<nama-fitur>      contoh: fitur/kelola-produk
perbaikan/<nama-bug>    contoh: perbaikan/validasi-form
```

**Langkah kerja**

1. `git checkout -b fitur/nama-fitur`
2. Kerjakan perubahan, lalu `git add . && git commit -m "feat: deskripsi singkat"`
3. `git push origin fitur/nama-fitur`
4. Buat Pull Request ke branch `main`.
5. Minta review ke Project Manager sebelum di-merge.

**Konvensi pesan commit**

| Tipe        | Keterangan                                |
| :---------- | :---------------------------------------- |
| `feat:`     | Menambah fitur baru                       |
| `fix:`      | Memperbaiki bug                           |
| `style:`    | Perubahan tampilan/styling                |
| `refactor:` | Perbaikan struktur kode tanpa ubah fungsi |
| `docs:`     | Perubahan dokumentasi                     |
| `test:`     | Menambah/memperbaiki pengujian            |

</details>

---

## ✅ Checklist Fitur

Gunakan checklist ini untuk memantau progres. Centang dengan mengubah `[ ]` menjadi `[x]`.

### Backend (Dimas)

- [ ] Membuat migrasi tabel utama
- [ ] Membuat Model dan relasi Eloquent
- [ ] Membuat seeder data contoh
- [ ] Membuat logika komponen Livewire
- [ ] Menambahkan validasi input

### Frontend (Rasta & Hafiz)

- [ ] Menyusun layout utama (header, footer)
- [ ] Membuat komponen Livewire untuk tampilan
- [ ] Styling halaman dengan Tailwind CSS
- [ ] Membuat tampilan responsif (mobile & desktop)
- [ ] Menambahkan umpan balik UI (loading, notifikasi)

### Bersama

- [ ] Integrasi backend & frontend
- [ ] Pengujian fitur (Pest)
- [ ] Finalisasi dokumentasi

---

## 📜 Perintah Penting

| Perintah                  | Fungsi                                      |
| :------------------------ | :------------------------------------------ |
| `composer run dev`        | Menjalankan server + Vite + queue sekaligus |
| `composer run setup`      | Setup awal proyek dari nol                  |
| `composer run test`       | Lint + static analysis + menjalankan tes    |
| `vendor/bin/pint --dirty` | Memperbaiki format kode PHP                 |
| `npm run dev`             | Menjalankan Vite (hot reload)               |
| `npm run build`           | Build aset untuk produksi                   |
| `php artisan migrate`     | Menjalankan migrasi database                |
| `php artisan route:list`  | Melihat daftar rute                         |

---

## 📌 Aturan Kontribusi

1. Selalu tarik perubahan terbaru (`git pull`) sebelum mulai bekerja.
2. Satu fitur = satu branch = satu Pull Request.
3. Jangan push langsung ke `main`.
4. Jalankan `vendor/bin/pint --dirty` sebelum commit kode PHP.
5. Sertakan deskripsi jelas pada setiap Pull Request.

---

<div align="center">

**Praktikum Pemrograman Web 2** · Dibuat oleh Tim: **PM · Dimas · Rasta · Hafiz**

</div>
