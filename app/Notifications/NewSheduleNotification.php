<?php

namespace App\Notifications;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSheduleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lesson;

    /**
     * Create a new notification instance.
     */
    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau Emploi du temps')
            ->view('emails.new-shedule', [
                'teacher' => $notifiable,
                'lesson' => $this->lesson,
                'weekDay' => Lesson::WEEKDAYS[$this->lesson->weekday],
                'startTime' => $this->lesson->start_time->format('H:i'),
                'endTime' => $this->lesson->end_time->format('H:i'),
                'parcourName' => $this->lesson->parcour->name,
                'niveauName' => $this->lesson->niveau->name,
                'programme' => $this->lesson->programme,
                'ue' => $this->lesson->programme->parent,
                'url' => route('teacher.timetable') // Assurez-vous que cette route existe
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'lesson_id' => $this->lesson->id,
            'weekday' => $this->lesson->weekday,
            'start_time' => $this->lesson->start_time,
            'end_time' => $this->lesson->end_time,
            'salle' => $this->lesson->salle,
            'type_cours' => $this->lesson->type_cours,
        ];
    }
}
