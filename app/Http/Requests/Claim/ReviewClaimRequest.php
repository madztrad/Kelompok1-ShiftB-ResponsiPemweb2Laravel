<?php

namespace App\Http\Requests\Claim;

use App\Enums\ClaimStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                ClaimStatus::Accepted->value,
                ClaimStatus::Rejected->value,
            ])],
        ];
    }
}
