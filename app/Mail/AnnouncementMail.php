<?php

namespace App\Mail;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Announcement $announcement,
        public ?string $receiverName = null
    ) {}

    public function build(): self
    {
        return $this
            ->subject('EPIRC — Nouvelle annonce : ' . $this->announcement->title)
            ->markdown('emails.announcement-published', [
                'announcement' => $this->announcement,
                'receiverName' => $this->receiverName,
            ]);
    }
}