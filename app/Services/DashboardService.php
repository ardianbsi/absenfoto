<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Enums\LeaveStatus;
use App\Enums\OvertimeStatus;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSuperAdminDashboard(): array
    {
        $stats = $this->getBaseStats();
        $stats['departmentStats'] = $this->getDepartmentStats();
        $stats['employeeOnLeave'] = $this->getEmployeesOnLeave();
        $stats['pendingLeaves'] = LeaveRequest::pending()->get();
        $stats['pendingOvertimes'] = OvertimeRequest::pending()->get();
        $stats['lateAlerts'] = $this->getLateAlerts();

        return $stats;
    }

    public function getHrDashboard(): array
    {
        $stats = $this->getBaseStats();
        $stats['departmentStats'] = $this->getDepartmentStats();
        $stats['employeeOnLeave'] = $this->getEmployeesOnLeave();
        $stats['pendingLeaves'] = LeaveRequest::pending()->get();
        $stats['pendingOvertimes'] = OvertimeRequest::pending()->get();
        $stats['lateAlerts'] = $this->getLateAlerts();

        return $stats;
    }

    public function getManagerDashboard(int $userId): array
    {
        $user = \App\Models\User::findOrFail($userId);
        $employee = $user->employee;

        $teamIds = Employee::where('manager_id', $employee?->id)->pluck('id');

        $today = now()->toDateString();

        $totalHadir = Attendance::whereIn('employee_id', $teamIds)
            ->whereDate('date', $today)
            ->where('status', AttendanceStatus::Hadir->value)
            ->count();

        $totalTelat = Attendance::whereIn('employee_id', $teamIds)
            ->whereDate('date', $today)
            ->where('status', AttendanceStatus::Telat->value)
            ->count();

        $totalTidakHadir = Employee::whereIn('id', $teamIds)
            ->where('is_active', true)
            ->whereDoesntHave('attendances', function ($q) use ($today) {
                $q->whereDate('date', $today);
            })->count();

        $totalCuti = LeaveRequest::whereIn('employee_id', $teamIds)
            ->where('status', LeaveStatus::Approved->value)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $totalWFH = Attendance::whereIn('employee_id', $teamIds)
            ->whereDate('date', $today)
            ->where('status', AttendanceStatus::WFH->value)
            ->count();

        $totalLembur = OvertimeRequest::whereIn('employee_id', $teamIds)
            ->whereDate('date', $today)
            ->where('status', OvertimeStatus::Approved->value)
            ->count();

        return [
            'totalHadir'      => $totalHadir,
            'totalTelat'      => $totalTelat,
            'totalTidakHadir' => $totalTidakHadir,
            'totalCuti'       => $totalCuti,
            'totalWFH'        => $totalWFH,
            'totalLembur'     => $totalLembur,
            'employeeOnLeave' => $this->getEmployeesOnLeave($teamIds->toArray()),
            'pendingLeaves'   => LeaveRequest::whereIn('employee_id', $teamIds)->pending()->get(),
            'pendingOvertimes' => OvertimeRequest::whereIn('employee_id', $teamIds)->pending()->get(),
            'lateAlerts'      => $this->getLateAlerts($teamIds->toArray()),
            'attendanceTrend' => $this->getAttendanceTrend(null, $teamIds->toArray()),
            'departmentStats' => [],
        ];
    }

    public function getEmployeeDashboard(int $userId): array
    {
        $user = \App\Models\User::findOrFail($userId);
        $employee = $user->employee;

        if (!$employee) {
            return $this->emptyDashboard();
        }

        $today = now()->toDateString();
        $employeeId = $employee->id;

        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->first();

        $hadirCount = Attendance::where('employee_id', $employeeId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->whereIn('status', [AttendanceStatus::Hadir->value, AttendanceStatus::Telat->value])
            ->count();

        $telatCount = Attendance::where('employee_id', $employeeId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', AttendanceStatus::Telat->value)
            ->count();

        $totalDays = now()->daysInMonth;
        $totalTidakHadir = $totalDays - $hadirCount;

        return [
            'totalHadir'        => $hadirCount,
            'totalTelat'        => $telatCount,
            'totalTidakHadir'   => max(0, $totalTidakHadir),
            'totalCuti'         => LeaveRequest::where('employee_id', $employeeId)
                ->where('status', LeaveStatus::Approved->value)
                ->whereYear('start_date', now()->year)
                ->sum('total_days'),
            'totalWFH'          => Attendance::where('employee_id', $employeeId)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->where('status', AttendanceStatus::WFH->value)
                ->count(),
            'totalLembur'       => OvertimeRequest::where('employee_id', $employeeId)
                ->where('status', OvertimeStatus::Approved->value)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('total_minutes'),
            'employeeOnLeave'   => [],
            'pendingLeaves'     => LeaveRequest::where('employee_id', $employeeId)->pending()->get(),
            'pendingOvertimes'  => OvertimeRequest::where('employee_id', $employeeId)->pending()->get(),
            'lateAlerts'        => $attendance && $attendance->status === AttendanceStatus::Telat->value
                ? [['date' => $today, 'minutes' => $attendance->late_minutes]]
                : [],
            'attendanceTrend'   => $this->getAttendanceTrend($employeeId),
            'departmentStats'   => [],
            'todayAttendance'   => $attendance,
        ];
    }

    protected function getBaseStats(): array
    {
        $today = now()->toDateString();

        $totalHadir = Attendance::whereDate('date', $today)
            ->where('status', AttendanceStatus::Hadir->value)
            ->count();

        $totalTelat = Attendance::whereDate('date', $today)
            ->where('status', AttendanceStatus::Telat->value)
            ->count();

        $totalTidakHadir = Employee::where('is_active', true)
            ->whereDoesntHave('attendances', function ($q) use ($today) {
                $q->whereDate('date', $today);
            })->count();

        $totalCuti = LeaveRequest::where('status', LeaveStatus::Approved->value)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $totalWFH = Attendance::whereDate('date', $today)
            ->where('status', AttendanceStatus::WFH->value)
            ->count();

        $totalLembur = OvertimeRequest::whereDate('date', $today)
            ->where('status', OvertimeStatus::Approved->value)
            ->count();

        return [
            'totalHadir'      => $totalHadir,
            'totalTelat'      => $totalTelat,
            'totalTidakHadir' => $totalTidakHadir,
            'totalCuti'       => $totalCuti,
            'totalWFH'        => $totalWFH,
            'totalLembur'     => $totalLembur,
            'attendanceTrend' => $this->getAttendanceTrend(),
            'recentAttendances' => Attendance::with('employee.user', 'employee.department')
                ->whereDate('date', $today)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }

    protected function getAttendanceTrend(?int $employeeId = null, ?array $employeeIds = null): array
    {
        $query = Attendance::select(
            DB::raw('DATE(date) as date'),
            DB::raw("COUNT(CASE WHEN status = 'hadir' THEN 1 END) as hadir"),
            DB::raw("COUNT(CASE WHEN status = 'telat' THEN 1 END) as telat"),
            DB::raw("COUNT(CASE WHEN status = 'alpha' THEN 1 END) as alpha")
        )
            ->whereDate('date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($employeeIds) {
            $query->whereIn('employee_id', $employeeIds);
        }

        return $query->get()->toArray();
    }

    protected function getDepartmentStats(): array
    {
        $today = now()->toDateString();

        return Department::withCount(['employees' => function ($q) {
            $q->where('is_active', true);
        }])
            ->get()
            ->map(function ($dept) use ($today) {
                $employeeIds = $dept->employees()->where('is_active', true)->pluck('id');

                $hadir = Attendance::whereIn('employee_id', $employeeIds)
                    ->whereDate('date', $today)
                    ->where('status', AttendanceStatus::Hadir->value)
                    ->count();

                $telat = Attendance::whereIn('employee_id', $employeeIds)
                    ->whereDate('date', $today)
                    ->where('status', AttendanceStatus::Telat->value)
                    ->count();

                return [
                    'department' => $dept->name,
                    'total'      => $dept->employees_count,
                    'hadir'      => $hadir,
                    'telat'      => $telat,
                    'alpha'      => max(0, $dept->employees_count - $hadir - $telat),
                ];
            })->toArray();
    }

    protected function getEmployeesOnLeave(?array $employeeIds = null): \Illuminate\Support\Collection
    {
        $today = now()->toDateString();

        $query = LeaveRequest::with('employee.user', 'employee.department', 'leaveType')
            ->where('status', LeaveStatus::Approved->value)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);

        if ($employeeIds) {
            $query->whereIn('employee_id', $employeeIds);
        }

        return $query->get();
    }

    protected function getLateAlerts(?array $employeeIds = null): \Illuminate\Support\Collection
    {
        $today = now()->toDateString();

        $query = Attendance::with('employee.user', 'employee.department')
            ->whereDate('date', $today)
            ->where('status', AttendanceStatus::Telat->value)
            ->where('late_minutes', '>', 0);

        if ($employeeIds) {
            $query->whereIn('employee_id', $employeeIds);
        }

        return $query->get();
    }

    protected function emptyDashboard(): array
    {
        return [
            'totalHadir'      => 0,
            'totalTelat'      => 0,
            'totalTidakHadir' => 0,
            'totalCuti'       => 0,
            'totalWFH'        => 0,
            'totalLembur'     => 0,
            'employeeOnLeave' => collect([]),
            'pendingLeaves'   => collect([]),
            'pendingOvertimes' => collect([]),
            'lateAlerts'      => collect([]),
            'attendanceTrend' => [],
            'departmentStats' => [],
        ];
    }
}
