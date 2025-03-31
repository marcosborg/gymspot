<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentedSlotNotification extends Notification
{
    use Queueable;

    private $rented_slot;

    /**
     * Create a new notification instance.
     */
    public function __construct($rented_slot)
    {
        $this->rented_slot = $rented_slot;
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
            ->subject('Rented Slot Notification')
            ->line('You have a new rented slot.')
            ->line('Slot ID: ' . $this->rented_slot->id)
            ->line('Start Date: ' . $this->rented_slot->start_date_time)
            ->line('End Date: ' . $this->rented_slot->end_date_time)
            ->line('Keypass: ' . $this->rented_slot->keypass)
            ->line('Client: ' . $this->rented_slot->client->name)
            ->line('You can view your rented slot details in your account.')
            ->action('Notification Action', url('https://gymspot.pt/admin'))
            ->line('Thank you for using our application!');
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
