<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'        => ['required', 'date'],
            'start_time'  => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time'    => ['required', 'date_format:Y-m-d H:i:s', 'after:start_time'],
            'description' => ['required', 'string', 'max:1000'],
            'attachment'  => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
        ];
    }
}
