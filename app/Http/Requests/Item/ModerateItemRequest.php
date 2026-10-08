<?php

namespace App\Http\Requests\Item;

use App\Enums\ModerationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModerateItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                ModerationStatus::Approved->value,
                ModerationStatus::Blocked->value,
            ])],
            'reason' => ['required_if:status,blocked', 'nullable', 'string', 'max:500'],
        ];
    }
}