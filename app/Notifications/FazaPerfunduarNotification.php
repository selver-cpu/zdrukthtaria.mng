<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FazaPerfunduarNotification extends Notification
{
    use Queueable;

    protected $projekt;
    protected $faza;
    protected $perdoruesi;

    /**
     * Create a new notification instance.
     */
    public function __construct($projekt, $faza, $perdoruesi)
    {
        $this->projekt = $projekt;
        $this->faza = $faza;
        $this->perdoruesi = $perdoruesi;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Faza e projektit ka përfunduar')
                    ->greeting('Përshëndetje!')
                    ->line($this->perdoruesi->emri . ' ka përfunduar fazën "' . $this->faza->emri_fazes . '" në projektin: ' . $this->projekt->emri_projektit)
                    ->line('Detajet e fazës:')
                    ->line('- Projekti: ' . $this->projekt->emri_projektit)
                    ->line('- Klienti: ' . $this->projekt->klienti->person_kontakti)
                    ->line('- Faza e përfunduar: ' . $this->faza->emri_fazes)
                    ->action('Shiko Projektin', url('/projektet/' . $this->projekt->projekt_id))
                    ->line('Ju lutemi kontrolloni detajet e projektit për të parë progresin.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'mesazhi' => $this->perdoruesi->emri . ' ka përfunduar fazën "' . $this->faza->emri_fazes . '" në projektin: ' . $this->projekt->emri_projektit,
            'projekt_id' => $this->projekt->projekt_id,
            'lloji_njoftimit' => 'system'
        ];
    }
}
