<?php

namespace Illuminate\Auth\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * name of user.
     *
     * @var string
     */
    public $username;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @param  string  $user_name
     * @return void
     */
    public function __construct($token, $user_name)
    {
        $this->token = $token;
        $this->username = $user_name;
    }


    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hello '.$this->username.',')
            ->line('You have now been granted access to the MyTownServices website.')
            ->line('Please use the below link to set a new password.')
            ->action('Set Password', url('password/set', $this->token))
            ->line('If you did not request a password, no further action is required.');
    }
}
