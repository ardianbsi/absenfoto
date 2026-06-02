<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use Carbon\Carbon;

class ReportService
{
    public function getDailyReport(string $date): array
    {
        $date = Carbon::parse($date)->toDateString();

        $attendances = Attendance::with('employee.department', 'employee.position')
            ->whereDate('date', $date)
            ->get();

        $total = Employee::where('is_active', true)->count();
        $hadir = $attendances->where('status', AttendanceStatus::Hadir->value)->count();
        $telat = $attendances->where('status', AttendanceStatus::Telat->value)->count();
        $wfh = $attendances->where('status', AttendanceStatus::WFH->value)->count();
        $alpha = max(0, $total - $hadir - $telat - $wfh);

        return [
            'date'        => $date,
            'total'       => $total,
            'hadir'       => $hadir,
            'telat'       => $telat,
            'wfh'         => $wfh,
            'alpha'       => $alpha,
            'attendances' => $attendances,
        ];
    }

    public function getWeeklyReport(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $attendances = Attendance::with('employee.department', 'employee.position')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('date');

        $leaves = LeaveRequest::with('employee', 'leaveType')
            ->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
            ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $overtimes = OvertimeRequest::with('employee')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        return [
            'start_date'  => $start->toDateString(),
            'end_date'    => $end->toDateString(),
            'attendances' => $attendances,
            'leaves'      => $leaves,
            'overtimes'   => $overtimes,
        ];
    }

    public function getMonthlyReport(int $month, int $year): array
    {
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->endOfMonth();

        $attendances = Attendance::with('employee.department', 'employee.position')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $leaves = LeaveRequest::with('employee', 'leaveType')
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->get();

        $overtimes = OvertimeRequest::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $total = Employee::where('is_active', true)->count();

        $summary = [
            'total_employees' => $total,
            'total_hadir'     => $attendances->where('status', AttendanceStatus::Hadir->value)->count(),
            'total_telat'     => $attendances->where('status', AttendanceStatus::Telat->value)->count(),
            'total_wfh'       => $attendances->where('status', AttendanceStatus::WFH->value)->count(),
            'total_alpha'     => $attendances->where('status', AttendanceStatus::Alpha->value)->count(),
            'total_leaves'    => $leaves->count(),
            'total_overtimes' => $overtimes->count(),
        ];

        return [
            'month'       => $month,
            'year'        => $year,
            'summary'     => $summary,
            'attendances' => $attendances,
            'leaves'      => $leaves,
            'overtimes'   => $overtimes,
        ];
    }

    public function getLateReport(string $startDate, string $endDate, ?int $employeeId = null): array
    {
        $query = Attendance::with('employee.department', 'employee.position')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', AttendanceStatus::Telat->value)
            ->where('late_minutes', '>', 0);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $records = $query->orderBy('date')->get();

        return [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'total'      => $records->count(),
            'records'    => $records,
        ];
    }

    public function getOvertimeReport(string $startDate, string $endDate, ?int $employeeId = null): array
    {
        $query = OvertimeRequest::with('employee.department', 'employee.position')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $records = $query->orderBy('date')->get();

        return [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'total'      => $records->count(),
            'records'    => $records,
        ];
    }

    public function getLeaveReport(string $startDate, string $endDate, ?int $employeeId = null): array
    {
        $query = LeaveRequest::with('employee.department', 'leaveType')
            ->whereBetween('start_date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $records = $query->orderBy('start_date')->get();

        return [
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'total'      => $records->count(),
            'records'    => $records,
        ];
    }

    public function getEmployeeAttendanceReport(int $employeeId, string $startDate, string $endDate): array
    {
        $employee = Employee::with('department', 'position')->findOrFail($employeeId);

        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $leaves = LeaveRequest::where('employee_id', $employeeId)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orderBy('start_date')
            ->get();

        $overtimes = OvertimeRequest::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $presentDays = $attendances->whereIn('status', [
            AttendanceStatus::Hadir->value,
            AttendanceStatus::Telat->value,
        ])->count();

        return [
            'employee'    => $employee,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'total_days'  => $totalDays,
            'hadir'       => $attendances->where('status', AttendanceStatus::Hadir->value)->count(),
            'telat'       => $attendances->where('status', AttendanceStatus::Telat->value)->count(),
            'alpha'       => max(0, $totalDays - $presentDays),
            'attendances' => $attendances,
            'leaves'      => $leaves,
            'overtimes'   => $overtimes,
        ];
    }
}
