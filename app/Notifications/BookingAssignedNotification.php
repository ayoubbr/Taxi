<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingAssignedNotification extends Notification
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('driver.dashboard');

        return (new MailMessage)
            ->subject('Congratulations! You Have a New Ride')
            ->greeting('Hello ' . $notifiable->firstname . ',')
            ->line('Your application for a booking has been accepted by the client!')
            ->line('**Booking Details:**')
            ->line('Pickup: ' . $this->booking->pickup_location)
            ->line('Destination: ' . $this->booking->destination)
            ->line('Time: ' . $this->booking->pickup_datetime->format('d M Y, H:i'))
            ->action('View My Dashboard', $url)
            ->line('Please be prepared for the scheduled pickup time. Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
