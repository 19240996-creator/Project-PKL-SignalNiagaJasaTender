<?php

namespace App\Console\Commands;

use App\Models\Tender;
use App\Models\User;
use App\Notifications\TenderDeadlineReminder;
use Illuminate\Console\Command;

class SendTenderDeadlineReminders extends Command
{
    protected $signature = 'tenders:deadline-reminders {--days=14 : Jumlah hari ke depan untuk diperiksa}';
    protected $description = 'Mengirim reminder database untuk tender yang mendekati deadline';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $tenders = Tender::whereNotNull('deadline')
            ->whereBetween('deadline', [today(), today()->addDays($days)])
            ->whereNotIn('status', ['Menang', 'Kalah', 'Selesai', 'Batal', 'Kontrak'])
            ->get(['id', 'tender_number', 'name', 'deadline', 'status'])
            ->map(fn (Tender $tender) => [
                'id' => $tender->id,
                'tender_number' => $tender->tender_number,
                'name' => $tender->name,
                'deadline' => $tender->deadline->toDateString(),
                'status' => $tender->status,
            ])->all();

        if ($tenders === []) {
            $this->info('Tidak ada tender yang mendekati deadline.');
            return self::SUCCESS;
        }

        $recipients = User::where('is_active', true)
            ->whereHas('role', fn ($query) => $query->whereIn('name', ['super_admin', 'management', 'tender_officer']))
            ->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new TenderDeadlineReminder($tenders));
        }

        $this->info("Reminder dikirim ke {$recipients->count()} user untuk " . count($tenders) . ' tender.');
        return self::SUCCESS;
    }
}