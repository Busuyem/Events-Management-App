<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Booking is Confirmed!')
            ->greeting('Hello ' . $notifiable->name)
            ->line("Your booking (#{$this->booking->id}) for event '{$this->booking->ticket->event->title}' has been confirmed.")
            ->line('Tickets booked: ' . $this->booking->quantity)
            ->line('Enjoy the event!');
    }
}
