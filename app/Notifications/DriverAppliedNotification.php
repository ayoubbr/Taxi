<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DriverAppliedNotification extends Notification
{
    use Queueable;

    protected $booking;
    protected $driver;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Booking $booking
     * @param \App\Models\User    $driver
     * @return void
     */
    public function __construct(Booking $booking, User $driver)
    {
        $this->booking = $booking;
        $this->driver = $driver;
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
        // This generates the URL for the client to see all applications for their booking
        $url = route('client.bookings.applications', ['booking' => $this->booking->id]);

        return (new MailMessage)
            ->subject('A Driver Has Applied for Your Booking!')
            ->greeting('Hello ' . $notifiable->firstname . ',')
            ->line('Good news! A driver has applied for your upcoming trip.')
            ->line('Driver Name: ' . $this->driver->firstname . ' ' . $this->driver->lastname)
            ->action('View Applications', $url)
            ->line('You can now review the application and assign the driver to your booking.');
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
