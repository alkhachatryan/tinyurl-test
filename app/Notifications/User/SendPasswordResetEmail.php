<?php

namespace App\Notifications\User;

use App\Models\Token;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendPasswordResetEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected Token $token)
    {}

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
     * @param User $user
     * @return MailMessage
     */
    public function toMail(User $user)
    {
        return (new MailMessage)
            ->subject('Password Reset')
            ->line('Click on the button below to reset your password')
            ->action('Reset Password', route('password-reset', ['token' => $this->token->encrypt()]));
    }
}
