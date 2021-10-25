<?php

namespace VanguardLTE\Listeners\Registration;

use VanguardLTE\Events\User\Registered;
use VanguardLTE\Repositories\User\UserRepository;
use VanguardLTE\Jobs\SendConfirmationMailJob;

class SendConfirmationEmail
{
    /**
     * @var UserRepository
     */
    private $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        try{
            if (! settings('use_email')) {
                return;
            }
            $user = $event->getRegisteredUser();
            SendConfirmationMailJob::dispatch($user);
        }
        catch(Throwable $e){

        }
    }
}
