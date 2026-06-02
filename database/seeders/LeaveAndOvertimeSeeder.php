<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use Illuminate\Database\Seeder;

class LeaveAndOvertimeSeeder extends Seeder
{
    public function run(): void
    {
        $this->createLeaveTypes();
        $this->createLeaveRequests();
        $this->createOvertimeRequests();
    }

    private function createLeaveTypes(): void
    {
        $types = [
            ['name' => 'Cuti Tahunan', 'code' => 'CT', 'description' => 'Cuti tahunan karyawan', 'quota' => 12, 'is_paid' => true, 'is_deduct_quota' => true, 'require_attachment' => false, 'is_active' => true, 'max_days' => 30],
            ['name' => 'Sakit', 'code' => 'SK', 'description' => 'Cuti karena sakit', 'quota' => 999, 'is_paid' => true, 'is_deduct_quota' => false, 'require_attachment' => true, 'is_active' => true, 'max_days' => 999],
            ['name' => 'Izin Pribadi', 'code' => 'IP', 'description' => 'Izin keperluan pribadi', 'quota' => 5, 'is_paid' => false, 'is_deduct_quota' => true, 'require_attachment' => false, 'is_active' => true, 'max_days' => 3],
            ['name' => 'Work From Home', 'code' => 'WFH', 'description' => 'Bekerja dari rumah', 'quota' => 5, 'is_paid' => true, 'is_deduct_quota' => true, 'require_attachment' => false, 'is_active' => true, 'max_days' => 999],
            ['name' => 'Dinas Luar', 'code' => 'DL', 'description' => 'Perjalanan dinas luar kantor', 'quota' => 999, 'is_paid' => true, 'is_deduct_quota' => false, 'require_attachment' => false, 'is_active' => true, 'max_days' => 999],
        ];

        foreach ($types as $data) {
            LeaveType::create($data);
        }
    }

    private function createLeaveRequests(): void
    {
        $employees = Employee::where('id', '>', 3)->get();
        $leaveTypeIds = LeaveType::pluck('id')->toArray();
        $statuses = ['pending', 'approved', 'approved', 'approved', 'rejected'];

        $reasons = [
            'Acara keluarga (pernikahan saudara kandung)',
            'Sakit demam tinggi dan tidak bisa beraktivitas',
            'Izin pribadi mengurus administrasi bank',
            'Ada acara adat keluarga besar',
            'Menjenguk orang tua yang sakit di kampung',
            'Ada acara khitanan anak',
            'Mengantar istri melahirkan',
            'Ada musibah di keluarga (kemalangan)',
            'Ibadah keagamaan tahunan',
            'Ada urusan mendadak di rumah',
            'Anak sakit dan perlu pengawasan',
            'Pindahan rumah',
            'Mengurus perpanjangan paspor',
            'Ada acara syukuran rumah baru',
            'Konsultasi kesehatan di rumah sakit',
            'Mendampingi orang tua check-up kesehatan',
            'Ada acara perpisahan sekolah anak',
            'Mengurus sertifikat tanah',
            'Ada rapat koordinasi di Dinas terkait',
            'Pelatihan dan workshop eksternal',
        ];

        for ($i = 0; $i < 20; $i++) {
            $employee = $employees->random();
            $leaveTypeId = $leaveTypeIds[array_rand($leaveTypeIds)];
            $leaveType = LeaveType::find($leaveTypeId);
            $maxDays = $leaveType && $leaveType->max_days ? $leaveType->max_days : 5;
            $totalDays = rand(1, min(3, $maxDays));
            $startDate = now()->subDays(rand(0, 60))->addDays(rand(0, 30));
            $endDate = (clone $startDate)->addDays($totalDays - 1);

            if ($endDate->isWeekend()) {
                $endDate->addDays(2);
            }

            LeaveRequest::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'total_days' => $totalDays,
                'reason' => $reasons[$i],
                'status' => $statuses[$i % count($statuses)],
                'approved_by' => in_array($statuses[$i % count($statuses)], ['approved', 'rejected']) ? 1 : null,
                'approved_at' => in_array($statuses[$i % count($statuses)], ['approved', 'rejected']) ? now() : null,
                'rejected_reason' => $statuses[$i % count($statuses)] === 'rejected' ? 'Mohon ajukan ulang dengan alasan yang lebih jelas' : null,
            ]);
        }
    }

    private function createOvertimeRequests(): void
    {
        $employees = Employee::where('id', '>', 3)->get();
        $statuses = ['pending', 'approved', 'approved', 'rejected'];

        $descriptions = [
            'Menyelesaikan laporan bulanan yang belum selesai',
            'Meeting deadline project dengan client',
            'Persiapan presentasi untuk meeting besok',
            'Menyelesaikan bug fixing aplikasi',
            'Penginputan data inventaris perusahaan',
            'Persiapan audit keuangan akhir bulan',
            'Menyelesaikan laporan keuangan sebelum deadline',
            'Meeting koordinasi dengan tim marketing',
            'Pengembangan fitur baru aplikasi',
            'Revisi dokumen tender proyek',
            'Persiapan event perusahaan',
            'Menyelesaikan backup data server',
            'Rollout update sistem ke seluruh cabang',
            'Monitoring sistem selama maintenance',
            'Persiapan laporan tahunan',
        ];

        for ($i = 0; $i < 15; $i++) {
            $employee = $employees->random();
            $startHour = rand(16, 19);
            $startMin = rand(0, 3) * 15;
            $duration = [60, 90, 120, 150, 180, 240][array_rand([60, 90, 120, 150, 180, 240])];
            $startTs = $startHour * 3600 + $startMin * 60;
            $endTs = $startTs + $duration * 60;
            $endHour = intdiv($endTs, 3600);
            $endMin = intdiv($endTs % 3600, 60);

            $overtimeDate = now()->subDays(rand(0, 30));
            $dateStr = $overtimeDate->format('Y-m-d');
            OvertimeRequest::create([
                'employee_id' => $employee->id,
                'date' => $dateStr,
                'start_time' => $dateStr . ' ' . sprintf('%02d:%02d:00', $startHour, $startMin),
                'end_time' => $dateStr . ' ' . sprintf('%02d:%02d:00', $endHour, $endMin),
                'total_minutes' => $duration,
                'description' => $descriptions[$i],
                'status' => $statuses[$i % count($statuses)],
                'approved_by' => in_array($statuses[$i % count($statuses)], ['approved', 'rejected']) ? 1 : null,
                'approved_at' => in_array($statuses[$i % count($statuses)], ['approved', 'rejected']) ? now() : null,
                'rejected_reason' => $statuses[$i % count($statuses)] === 'rejected' ? 'Overtime tidak disetujui, silakan koordinasi dengan atasan langsung' : null,
            ]);
        }
    }
}
