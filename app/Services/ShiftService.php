<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function assignShift(Employee $employee, Shift $shift, string $startDate, ?string $endDate = null): EmployeeShift
    {
        $employeeShift = EmployeeShift::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'start_date'  => $startDate,
            ],
            [
                'shift_id'  => $shift->id,
                'end_date'  => $endDate,
                'is_active' => true,
            ]
        );

        $this->generateScheduleRange($employee, $shift, $startDate, $endDate);

        try {
            $this->notificationService->sendShiftAssignment($employee, $shift);
        } catch (\Throwable $e) {
            report($e);
        }

        return $employeeShift;
    }

    public function assignShiftMass(array $employeeIds, Shift $shift, string $startDate, ?string $endDate = null): int
    {
        $count = 0;

        DB::transaction(function () use ($employeeIds, $shift, $startDate, $endDate, &$count) {
            foreach ($employeeIds as $employeeId) {
                $employee = Employee::find($employeeId);
                if (!$employee) {
                    continue;
                }

                $this->assignShift($employee, $shift, $startDate, $endDate);
                $count++;
            }
        });

        return $count;
    }

    public function getEmployeeSchedule(Employee $employee, int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $schedules = ShiftSchedule::with('shift')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date')
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->toDateString();
            });

        $defaultShift = $employee->shift;

        $result = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dateStr = $current->toDateString();

            if (isset($schedules[$dateStr])) {
                $result[$dateStr] = $schedules[$dateStr];
            } elseif ($defaultShift) {
                $result[$dateStr] = (object) [
                    'date'        => $dateStr,
                    'shift'       => $defaultShift,
                    'is_override' => false,
                ];
            } else {
                $result[$dateStr] = (object) [
                    'date'        => $dateStr,
                    'shift'       => null,
                    'is_override' => false,
                ];
            }

            $current->addDay();
        }

        return $result;
    }

    public function getShiftSchedule(string $date): array
    {
        return ShiftSchedule::with('employee.department', 'shift')
            ->whereDate('date', $date)
            ->orderBy('shift_id')
            ->get()
            ->groupBy('shift_id')
            ->toArray();
    }

    public function autoRotateShift(): void
    {
        $rotatingShifts = Shift::where('type', 'rotating')->where('is_active', true)->get();

        if ($rotatingShifts->isEmpty()) {
            return;
        }

        $today = now()->toDateString();
        $employees = Employee::whereHas('shift', function ($q) {
            $q->where('type', 'rotating');
        })->where('is_active', true)->get();

        foreach ($employees as $employee) {
            $currentSchedule = ShiftSchedule::where('employee_id', $employee->id)
                ->whereDate('date', $today)
                ->first();

            if ($currentSchedule && $currentSchedule->is_override) {
                continue;
            }

            $lastSchedule = ShiftSchedule::where('employee_id', $employee->id)
                ->whereDate('date', '<', $today)
                ->orderBy('date', 'desc')
                ->first();

            $nextShift = $this->getNextRotatingShift($employee, $lastSchedule?->shift_id);
            $employeeShift = $employee->shift;

            if ($nextShift) {
                ShiftSchedule::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date'        => $today,
                    ],
                    [
                        'shift_id'    => $nextShift->id,
                        'is_override' => false,
                    ]
                );
            } elseif ($employeeShift) {
                ShiftSchedule::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'date'        => $today,
                    ],
                    [
                        'shift_id'    => $employeeShift->id,
                        'is_override' => false,
                    ]
                );
            }
        }
    }

    public function overrideShift(Employee $employee, Shift $shift, string $date): ShiftSchedule
    {
        return ShiftSchedule::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'date'        => $date,
            ],
            [
                'shift_id'    => $shift->id,
                'is_override' => true,
            ]
        );
    }

    protected function generateScheduleRange(Employee $employee, Shift $shift, string $startDate, ?string $endDate): void
    {
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : $start->copy()->addMonth();

        $current = $start->copy();
        while ($current->lte($end)) {
            ShiftSchedule::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date'        => $current->toDateString(),
                ],
                [
                    'shift_id'    => $shift->id,
                    'is_override' => false,
                ]
            );
            $current->addDay();
        }
    }

    protected function getNextRotatingShift(Employee $employee, ?int $lastShiftId): ?Shift
    {
        $rotatingShifts = Shift::where('type', 'rotating')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        if ($rotatingShifts->isEmpty()) {
            return null;
        }

        if ($lastShiftId === null) {
            return $rotatingShifts->first();
        }

        $found = false;
        foreach ($rotatingShifts as $shift) {
            if ($found) {
                return $shift;
            }
            if ($shift->id === $lastShiftId) {
                $found = true;
            }
        }

        return $rotatingShifts->first();
    }
}
