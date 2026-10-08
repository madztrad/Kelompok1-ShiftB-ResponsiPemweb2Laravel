<?php

namespace App\Http\Requests\Item;

use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware & policy
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:2000'],
            'type' => ['required', Rule::enum(ItemType::class)],
            'location' => ['required', 'string', 'max:150'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'kategori',
            'title' => 'judul',
            'description' => 'deskripsi',
            'type' => 'tipe',
            'location' => 'lokasi',
            'event_date' => 'tanggal kejadian',
            'photo' => 'foto',
        ];
    }
}