<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'          => ['required', 'string', 'in:approved,rejected'],
            'notes'           => ['nullable', 'string', 'max:500'],
            'rejected_reason' => ['required_if:status,rejected', 'string', 'min:5', 'max:500'],
        ];
    }
}
