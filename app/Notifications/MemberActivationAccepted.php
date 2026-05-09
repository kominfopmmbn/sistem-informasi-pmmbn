<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberActivationAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    private readonly array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->replyTo($this->data['email'], $this->data['full_name'])
            ->subject('Aktivasi Anggota Telah Diverifikasi')
            ->greeting('Halo ' . $this->data['full_name'] . '!')
            ->line('Aktivasi Anggota Anda telah selesai diverifikasi.')
            ->line('Anda bisa melihat kartu tanda anggota Anda melalui link dibawah ini.')
            ->action('Kartu Tanda Anggota', 'https://google.com');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
