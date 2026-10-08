<?php

namespace App\Models;

use App\Enums\ItemType;
use App\Enums\ModerationStatus;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id', 'title', 'description', 'type', 'location', 'event_date', 'photo_path',
    'moderation_status', 'blocked_reason', 'moderated_by', 'moderated_at', 'resolved_at',
])]
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ItemType::class,
            'moderation_status' => ModerationStatus::class,
            'event_date' => 'date',
            'moderated_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // ---------------------------------------------------------------- Relations

    /** Pelapor / pemilik laporan. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Admin yang terakhir memoderasi. */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    // ------------------------------------------------------------------ Helpers

    public function isApproved(): bool
    {
        return $this->moderation_status === ModerationStatus::Approved;
    }

    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }

    // ------------------------------------------------------------------- Scopes

    /** Hanya laporan yang sudah disetujui admin (tampil di beranda). */
    public function scopeApproved(Builder $query): void
    {
        $query->where('moderation_status', ModerationStatus::Approved->value);
    }

    /**
     * Search + filter. Key yang didukung:
     * search, type, category_id, moderation_status, user_id, resolved, date_from, date_to
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn (Builder $q, $v) => $q->where('type', $v))
            ->when($filters['category_id'] ?? null, fn (Builder $q, $v) => $q->where('category_id', $v))
            ->when($filters['moderation_status'] ?? null, fn (Builder $q, $v) => $q->where('moderation_status', $v))
            ->when($filters['user_id'] ?? null, fn (Builder $q, $v) => $q->where('user_id', $v))
            ->when($filters['date_from'] ?? null, fn (Builder $q, $v) => $q->whereDate('event_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn (Builder $q, $v) => $q->whereDate('event_date', '<=', $v))
            ->when(
                array_key_exists('resolved', $filters),
                fn (Builder $q) => $filters['resolved']
                    ? $q->whereNotNull('resolved_at')
                    : $q->whereNull('resolved_at')
            );
    }
}