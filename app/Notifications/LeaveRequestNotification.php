<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\LeaveRequest;

class LeaveRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $leaveRequest;
    protected $type;
    
    public function __construct(LeaveRequest $leaveRequest, $type = 'submitted')
    {
        $this->leaveRequest = $leaveRequest;
        $this->type = $type;
    }
    
    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        $messages = [
            'submitted' => 'Pengajuan cuti baru dari ' . $this->leaveRequest->employee->full_name,
            'approved' => 'Pengajuan cuti Anda (' . $this->leaveRequest->leaveType->name . ') telah disetujui',
            'rejected' => 'Pengajuan cuti Anda (' . $this->leaveRequest->leaveType->name . ') ditolak: ' . $this->leaveRequest->rejected_reason,
            'cancelled' => 'Pengajuan cuti ' . $this->leaveRequest->employee->full_name . ' dibatalkan',
        ];
        
        return [
            'title' => 'Pengajuan Cuti',
            'message' => $messages[$this->type] ?? '',
            'type' => $this->type,
            'leave_id' => $this->leaveRequest->id,
            'url' => route('leave.show', $this->leaveRequest->id),
            'icon' => 'ti-calendar-check',
        ];
    }
}
