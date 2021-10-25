<?php

namespace VanguardLTE\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GetFreespin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $user;
    public function __construct($user)
    {
        //
        $this->user = $user;
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
        $welcomepackages = \VanguardLTE\WelcomePackage::leftJoin('games', function ($join)
        {
            $join->on('games.original_id','=','welcomepackages.game_id');
            $join->on('games.id','=','games.original_id');
        })->select('welcomepackages.*', 'games.name')->get();
        $subject = sprintf("%s - %s", settings('app_name'), trans('app.get_freespin'));
        return (new MailMessage)
            ->subject($subject)
            ->view('emails.freespin.get', ['username'=> $this->user->username, 'welcomepackages' => $welcomepackages]);
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
