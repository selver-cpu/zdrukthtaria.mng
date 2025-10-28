<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Projektet;

class NjoftimProjekti extends Notification implements ShouldQueue
{
    use Queueable;

    protected $projekt;
    protected $mesazhi;
    protected $tipi;

    /**
     * Create a new notification instance.
     */
    public function __construct(Projektet $projekt, string $mesazhi, string $tipi = 'përditësim')
    {
        $this->projekt = $projekt;
        $this->mesazhi = $mesazhi;
        $this->tipi = $tipi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Njoftim për projektin: ' . $this->projekt->emri_projektit)
            ->greeting('Përshëndetje ' . $notifiable->emri . ',')
            ->line($this->mesazhi)
            ->action('Shiko Projektin', route('projektet.show', $this->projekt))
            ->line('Faleminderit që përdorni sistemin tonë!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'projekt_id' => $this->projekt->id,
            'mesazhi' => $this->mesazhi,
            'tipi' => $this->tipi,
            'link' => route('projektet.show', $this->projekt),
        ];
    }
}
