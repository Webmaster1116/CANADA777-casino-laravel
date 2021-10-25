<?php

namespace VanguardLTE\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

class EmailConfirmation extends Notification
{
    /**
     * Email confirmation token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        try
        {
            $subject = sprintf("%s - %s", settings('app_name'), trans('app.registration_confirmation'));

            return (new MailMessage)
                ->subject($subject)
                ->line(trans('app.welcome_bonus_free_spin'))
                ->line(trans('app.welcome_to_casino', ['app' => settings('app_name')]))
                ->line(trans('app.start_today_and_claim'))
                ->action(trans('app.verify_account'), route('frontend.register.confirm-email', $this->token))
                ->line(trans('app.100%_upto_1000$_100_freespin'))
                ->line(trans('app.use_welcome_bonus'))
                ->line(trans('app.make_a_deposit'))
                ->line(trans('app.activate_bonus'))
                ->line(trans('app.enjoy_with_bonus'))
                ->line(trans('app.casino_team_wishes', ['app' => settings('app_name')]))
                ->line(new HtmlString('<a href="'.url('/bonus/term').'">'.trans('app.bonus_terms').'</a>'));
        }
        catch(\Exception $e){
            dump('Mail not sent');
        }
    }
}