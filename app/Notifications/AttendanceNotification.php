<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Attendance;

class AttendanceNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $attendance;
    protected $type;
    
    public function __construct(Attendance $attendance, $type = 'late')
    {
        $this->attendance = $attendance;
        $this->type = $type;
    }
    
    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        $messages = [
            'late' => 'Karyawan ' . $this->attendance->employee->full_name . ' terlambat ' . $this->attendance->late_minutes . ' menit',
            'reminder_checkin' => 'Jangan lupa melakukan check-in hari ini',
            'reminder_checkout' => 'Jangan lupa melakukan check-out hari ini',
        ];
        
        return [
            'title' => 'Pengingat Absensi',
            'message' => $messages[$this->type] ?? '',
            'type' => $this->type,
            'attendance_id' => $this->attendance->id,
            'url' => route('attendance.show', $this->attendance->id),
            'icon' => 'ti-alert-triangle',
        ];
    }
}
