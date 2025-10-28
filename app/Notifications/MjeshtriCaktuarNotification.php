<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MjeshtriCaktuarNotification extends Notification
{
    use Queueable;

    protected $projekt;
    protected $mjeshtri;
    protected $menaxheri;

    /**
     * Create a new notification instance.
     */
    public function __construct($projekt, $mjeshtri, $menaxheri)
    {
        $this->projekt = $projekt;
        $this->mjeshtri = $mjeshtri;
        $this->menaxheri = $menaxheri;
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
                    ->subject('Ju jeni caktuar në një projekt të ri')
                    ->greeting('Përshëndetje ' . $this->mjeshtri->emri . '!')
                    ->line($this->menaxheri->emri . ' ju ka caktuar si mjeshtër për projektin: ' . $this->projekt->emri_projektit)
                    ->line('Detajet e projektit:')
                    ->line('- Klienti: ' . $this->projekt->klienti->person_kontakti)
                    ->line('- Adresa: ' . $this->projekt->klienti->adresa_faktura)
                    ->line('- Qyteti: ' . $this->projekt->klienti->qyteti)
                    ->line('- Telefoni: ' . $this->projekt->klienti->telefon_kontakt)
                    ->action('Shiko Detajet e Projektit', url('/projektet/' . $this->projekt->projekt_id))
                    ->line('Ju lutemi të filloni planifikimin e punës sa më shpejt të jetë e mundur.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'mesazhi' => $this->menaxheri->emri . ' ju ka caktuar si mjeshtër për projektin: ' . $this->projekt->emri_projektit,
            'projekt_id' => $this->projekt->projekt_id,
            'lloji_njoftimit' => 'system'
        ];
    }
}
