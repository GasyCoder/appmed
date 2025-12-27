<?php

namespace App\Jobs;

use App\Mail\AnnouncementMail;
use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAnnouncementEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $announcementId;
    /** @var array<int,string> */
    public array $emails;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * @param  int  $announcementId
     * @param  array<int,string>  $emails
     */
    public function __construct(int $announcementId, array $emails)
    {
        $this->announcementId = $announcementId;
        $this->emails = $emails;
    }

    public function handle(): void
    {
        $announcement = Announcement::query()->find($this->announcementId);
        if (!$announcement) return;

        foreach ($this->emails as $email) {
            try {
                Mail::to($email)->send(new AnnouncementMail($announcement));
            } catch (\Throwable $e) {
                Log::error('SendAnnouncementEmailsJob error', [
                    'announcement_id' => $this->announcementId,
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
