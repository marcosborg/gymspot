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
            ->line('Entidade: ' . $this->payment_multibanco['Entity'])
            ->line('Referência: ' . $this->payment_multibanco['Reference'])
            ->line('Montante: ' . number_format($this->payment_multibanco['Amount'], 2, ',', '.') . ' €')
            ->line('A reserva ficará disponível no separador RESERVAS da app GymSpot após pagamento.')
            ->line('Por favor, efetue o pagamento até 24 horas após a geração desta referência.')
            ->line('A reserva só será efetivada após a confirmação do pagamento.');
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
