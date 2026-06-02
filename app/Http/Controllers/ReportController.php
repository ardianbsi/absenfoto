<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $departments = Department::active()->orderBy('name')->get();
        $employees = Employee::active()->orderBy('full_name')->get();
        $data = $this->reportService->getDailyReport(now()->toDateString());

        return view('reports.index', array_merge($data, compact('departments', 'employees')));
    }

    public function dailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $departmentId = $request->input('department_id');

        $data = $this->reportService->getDailyReport($date);

        if ($departmentId) {
            $data['attendances'] = $data['attendances']->filter(function ($attendance) use ($departmentId) {
                return $attendance->employee && $attendance->employee->department_id == $departmentId;
            });
        }

        return view('reports.index', array_merge($data, [
            'departments' => Department::active()->orderBy('name')->get(),
            'employees' => Employee::active()->orderBy('full_name')->get(),
            'departmentId' => $departmentId,
        ]));
    }

    public function weeklyReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', now()->endOfWeek()->toDateString());

        $data = $this->reportService->getWeeklyReport($startDate, $endDate);

        $employees = Employee::active()->where('is_active', true)->orderBy('full_name')->get();
        $report = [];
        foreach ($employees as $employee) {
            $employeeAttendances = collect();
            foreach ($data['attendances'] as $dateAttendances) {
                $employeeAttendances = $employeeAttendances->merge(
                    $dateAttendances->where('employee_id', $employee->id)
                );
            }
            $hadir = $employeeAttendances->where('status', 'hadir')->count();
            $telat = $employeeAttendances->where('status', 'telat')->count();
            $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $alpha = max(0, $totalDays - $hadir - $telat);

            $report[] = [
                'name' => $employee->full_name,
                'hadir' => $hadir,
                'telat' => $telat,
                'alpha' => $alpha,
                'total' => $hadir + $telat + $alpha,
            ];
        }

        return view('reports.index', compact('report', 'employees', 'startDate', 'endDate') + [
            'departments' => Department::active()->orderBy('name')->get(),
        ]);
    }

    public function monthlyReport(Request $request)
    {
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $data = $this->reportService->getMonthlyReport($month, $year);
        $totalHadir = $data['summary']['total_hadir'] ?? 0;
        $totalTelat = $data['summary']['total_telat'] ?? 0;
        $totalAlpha = $data['summary']['total_alpha'] ?? 0;

        return view('reports.index', array_merge($data, [
            'departments' => Department::active()->orderBy('name')->get(),
            'employees' => Employee::active()->orderBy('full_name')->get(),
            'totalHadir' => $totalHadir,
            'totalTelat' => $totalTelat,
            'totalAlpha' => $totalAlpha,
        ]));
    }

    public function lateReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $employeeId = $request->input('employee_id');

        $data = $this->reportService->getLateReport($startDate, $endDate, $employeeId ? (int) $employeeId : null);

        return view('reports.index', [
            'lateAttendances' => $data['records'],
            'employees' => Employee::active()->orderBy('full_name')->get(),
            'departments' => Department::active()->orderBy('name')->get(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function overtimeReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $employeeId = $request->input('employee_id');

        $data = $this->reportService->getOvertimeReport($startDate, $endDate, $employeeId ? (int) $employeeId : null);

        return view('reports.index', [
            'overtimeReports' => $data['records'],
            'employees' => Employee::active()->orderBy('full_name')->get(),
            'departments' => Department::active()->orderBy('name')->get(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function leaveReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $employeeId = $request->input('employee_id');

        $data = $this->reportService->getLeaveReport($startDate, $endDate, $employeeId ? (int) $employeeId : null);

        return view('reports.index', [
            'leaveReports' => $data['records'],
            'employees' => Employee::active()->orderBy('full_name')->get(),
            'departments' => Department::active()->orderBy('name')->get(),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function employeeReport(Request $request, $id)
    {
        $employee = Employee::with('department', 'position')->findOrFail($id);

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $data = $this->reportService->getEmployeeAttendanceReport((int) $id, $startDate, $endDate);

        return view('reports.index', array_merge($data, [
            'departments' => Department::active()->orderBy('name')->get(),
            'employees' => Employee::active()->orderBy('full_name')->get(),
        ]));
    }

    public function exportCSV(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', now()->toDateString());
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $data = match ($type) {
            'daily' => $this->reportService->getDailyReport($date),
            'monthly' => $this->reportService->getMonthlyReport($month, $year),
            default => $this->reportService->getDailyReport($date),
        };

        $filename = tempnam(sys_get_temp_dir(), 'report_') . '.csv';

        $handle = fopen($filename, 'w');
        fputcsv($handle, ['No', 'Nama', 'NIK', 'Departemen', 'Check In', 'Check Out', 'Status']);

        foreach ($data['attendances'] as $i => $attendance) {
            fputcsv($handle, [
                $i + 1,
                $attendance->employee?->full_name ?? '-',
                $attendance->employee?->nik ?? '-',
                $attendance->employee?->department?->name ?? '-',
                $attendance->check_in?->format('H:i:s') ?? '-',
                $attendance->check_out?->format('H:i:s') ?? '-',
                $attendance->status,
            ]);
        }

        fclose($handle);

        return response()->download($filename, "report-{$type}-{$date}.csv")->deleteFileAfterSend(true);
    }
}
