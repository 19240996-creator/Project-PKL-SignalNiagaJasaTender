<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TenderDeadlineReminder extends Notification
{
    use Queueable;

    public function __construct(private readonly array $tenders)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Reminder deadline tender',
            'message' => count($this->tenders) . ' tender mendekati deadline dalam 14 hari.',
            'tenders' => $this->tenders,
        ];
    }
}