<?php

namespace App\Http\Requests\Item;

use App\Enums\ItemType;
use App\Enums\ModerationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi query string untuk search / filter / pagination daftar barang.
 */
class ItemFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::enum(ItemType::class)],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'moderation_status' => ['nullable', Rule::enum(ModerationStatus::class)],
            'user_id' => ['nullable', 'integer'],
            'resolved' => ['nullable', 'boolean'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * Filter yang sudah dinormalisasi, siap dipakai Item::filter().
     *
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        $filters = $this->validated();

        if (($filters['resolved'] ?? null) === null) {
            unset($filters['resolved']);
        } else {
            $filters['resolved'] = $this->boolean('resolved');
        }

        return $filters;
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 12);
    }
}
