<?php

namespace VanguardLTE\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositSuccessed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $transaction;
    public function __construct($transaction)
    {
        //
        $this->transaction = $transaction;
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
        // $user = \VanguardLTE\User::where('id', $this->transaction->user_id)->first();
        $user = $this->transaction->user()->first();
        $subject = sprintf("%s - %s", settings('app_name'), trans('app.payment_success'));
        return (new MailMessage)
            ->subject($subject)
            ->view( 'emails.deposit.success', ['username'=> $user->username, 'amount' => $this->transaction->summ]);
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
