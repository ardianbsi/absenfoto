<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            'nik'            => ['required', 'string', 'max:20', Rule::unique('employees', 'nik')->ignore($employeeId)],
            'full_name'      => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user_id)],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:500'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'date_of_birth'  => ['nullable', 'date', 'before:today'],
            'gender'         => ['required', 'string', 'in:male,female'],
            'department_id'  => ['required', 'integer', 'exists:departments,id'],
            'position_id'    => ['required', 'integer', 'exists:positions,id'],
            'manager_id'     => ['nullable', 'integer', 'exists:employees,id'],
            'join_date'      => ['required', 'date'],
            'work_status'    => ['required', 'string', 'in:contract,permanent,intern,probation'],
            'shift_id'       => ['nullable', 'integer', 'exists:shifts,id'],
            'default_attendance_type' => ['nullable', 'string', 'in:wfo,waf,wfh'],
            'photo'          => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}
