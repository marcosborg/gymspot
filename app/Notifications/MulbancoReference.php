<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MulbancoReference extends Notification
{
    use Queueable;

    private $payment_multibanco;

    /**
     * Create a new notification instance.
     */
    public function __construct($payment_multibanco)
    {
        $this->payment_multibanco = $payment_multibanco;
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
            ->subject('Pagamento multibanco')
            ->line('<strong>Entidade: </strong> ' . $this->payment_multibanco['Entity'] . '<br>')
            ->line('<strong>Referência: </strong> ' . $this->payment_multibanco['Reference'] . '<br>')
            ->line('<strong>Montante: </strong> ' . number_format($this->payment_multibanco['Amount'], 2, ',', '.') . ' €')
            ->line('A reserva ficará disponível após pagamento.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
