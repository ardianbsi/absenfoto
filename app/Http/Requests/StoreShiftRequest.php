<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'code'              => ['required', 'string', 'max:20', 'unique:shifts,code'],
            'type'              => ['required', 'string', 'in:fixed,flexible,rotating'],
            'color'             => ['nullable', 'string', 'max:7'],
            'start_time'        => ['required', 'date_format:H:i'],
            'end_time'          => ['required_if:type,fixed', 'date_format:H:i', 'after:start_time'],
            'tolerance_minutes' => ['nullable', 'integer', 'min:0'],
            'description'       => ['nullable', 'string', 'max:500'],
        ];
    }
}
