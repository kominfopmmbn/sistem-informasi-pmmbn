<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberActivationRejected extends Notification implements ShouldQueue
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
            ->subject('Aktivasi Anggota Ditolak')
            ->greeting('Halo ' . $this->data['full_name'] . '!')
            ->line('Aktivasi Anggota Anda telah ditolak.')
            ->line('Alasan: ' . $this->data['notes'])
            ->line('Jika Anda ingin melakukan aktivasi ulang, silakan klik tombol dibawah ini.')
            ->action('Aktivasi Ulang', url(route('about.member-activation.index', ['member_activation_id' => encrypt($this->data['id'])])));
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
