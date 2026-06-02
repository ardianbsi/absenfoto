<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\OvertimeRequest;

class OvertimeRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $overtimeRequest;
    protected $type;
    
    public function __construct(OvertimeRequest $overtimeRequest, $type = 'submitted')
    {
        $this->overtimeRequest = $overtimeRequest;
        $this->type = $type;
    }
    
    public function via($notifiable)
    {
        return ['database'];
    }
    
    public function toDatabase($notifiable)
    {
        $messages = [
            'submitted' => 'Pengajuan lembur baru dari ' . $this->overtimeRequest->employee->full_name,
            'approved' => 'Pengajuan lembur Anda (' . $this->overtimeRequest->date->format('d/m/Y') . ') telah disetujui',
            'rejected' => 'Pengajuan lembur Anda (' . $this->overtimeRequest->date->format('d/m/Y') . ') ditolak: ' . $this->overtimeRequest->rejected_reason,
            'cancelled' => 'Pengajuan lembur ' . $this->overtimeRequest->employee->full_name . ' dibatalkan',
        ];
        
        return [
            'title' => 'Pengajuan Lembur',
            'message' => $messages[$this->type] ?? '',
            'type' => $this->type,
            'overtime_id' => $this->overtimeRequest->id,
            'url' => route('overtime.show', $this->overtimeRequest->id),
            'icon' => 'ti-clock-plus',
        ];
    }
}
