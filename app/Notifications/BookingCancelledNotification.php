<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
{
    use Queueable;

    protected $booking;
    protected $cancellerRole;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Booking $booking, string $cancellerRole)
    {
        $this->booking = $booking;
        $this->cancellerRole = $cancellerRole;
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
        $greeting = 'Hello ' . $notifiable->firstname . ',';
        $subject = 'A Booking Has Been Cancelled';
        $actionUrl = route('home'); // Default URL
        $actionText = 'Visit Our Website';

        if ($this->cancellerRole === 'client') {
            // Message for the DRIVER when a CLIENT cancels
            $line1 = 'We regret to inform you that the following booking has been cancelled by the client.';
            $actionUrl = route('driver.dashboard');
            $actionText = 'View My Dashboard';
        } else {
            // Message for the CLIENT when a DRIVER cancels
            $line1 = 'We regret to inform you that your assigned driver has cancelled the following booking.';
            $actionUrl = route('client.bookings.index');
            $actionText = 'View My Bookings';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line1)
            ->line('**Cancelled Booking Details:**')
            ->line('Pickup: ' . $this->booking->pickup_location . ' (' . $this->booking->pickup_city . ')')
            ->line('Destination: ' . $this->booking->destination)
            ->line('Originally Scheduled: ' . $this->booking->pickup_datetime->format('d M Y, H:i'))
            ->action($actionText, $actionUrl)
            ->line('We apologize for any inconvenience. For assistance, please contact our support team.');
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
