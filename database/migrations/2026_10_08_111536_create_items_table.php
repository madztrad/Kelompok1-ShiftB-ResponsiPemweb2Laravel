<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();

            $table->string('title', 120);
            $table->text('description');
            $table->string('type', 10)->index();          // lost | found
            $table->string('location', 150);
            $table->date('event_date');                   // tanggal hilang / ditemukan
            $table->string('photo_path')->nullable();

            // moderasi oleh admin: pending | approved | blocked
            $table->string('moderation_status', 20)->default('pending');
            $table->text('blocked_reason')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();

            // terisi saat barang sudah dikembalikan ke pemilik (klaim diterima)
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['moderation_status', 'type']);
            $table->index('event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};