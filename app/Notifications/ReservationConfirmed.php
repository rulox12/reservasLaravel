<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationConfirmed extends Notification
{
    use Queueable;
    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmación de Reserva')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Su reserva ha sido confirmada exitosamente.')
            ->line('Detalles de la reserva')
            ->line('Habitación: ' . $this->reservation->room->room_number)
            ->line('Fecha de inicio: ' . $this->reservation->check_in_date)
            ->line('Fecha de fin: ' . $this->reservation->check_out_date)
            ->line('Total a pagar: ' . $this->reservation->total_price)
            ->line('Gracias por elegirnos.')
            ->salutation('Saludos, Hotel La Colina');
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
