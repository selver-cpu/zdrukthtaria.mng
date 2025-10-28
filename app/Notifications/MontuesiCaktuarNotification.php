<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MontuesiCaktuarNotification extends Notification
{
    use Queueable;

    protected $projekt;
    protected $montuesi;
    protected $menaxheri;

    /**
     * Create a new notification instance.
     */
    public function __construct($projekt, $montuesi, $menaxheri)
    {
        $this->projekt = $projekt;
        $this->montuesi = $montuesi;
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
                    ->subject('Ju jeni caktuar në një projekt të ri si montues')
                    ->greeting('Përshëndetje ' . $this->montuesi->emri . '!')
                    ->line($this->menaxheri->emri . ' ju ka caktuar si montues për projektin: ' . $this->projekt->emri_projektit)
                    ->line('Detajet e projektit:')
                    ->line('- Klienti: ' . $this->projekt->klienti->person_kontakti)
                    ->line('- Adresa: ' . $this->projekt->klienti->adresa_faktura)
                    ->line('- Qyteti: ' . $this->projekt->klienti->qyteti)
                    ->line('- Telefoni: ' . $this->projekt->klienti->telefon_kontakt)
                    ->action('Shiko Detajet e Projektit', url('/projektet/' . $this->projekt->projekt_id))
                    ->line('Ju lutemi të koordinoheni me mjeshtrin për planifikimin e punës.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'mesazhi' => $this->menaxheri->emri . ' ju ka caktuar si montues për projektin: ' . $this->projekt->emri_projektit,
            'projekt_id' => $this->projekt->projekt_id,
            'lloji_njoftimit' => 'system'
        ];
    }
}
