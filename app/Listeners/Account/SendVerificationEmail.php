<?php

namespace App\Listeners\Account;


use App\Jobs\SendValidationEmail;

class SendVerificationEmail
{
    //use InteractsWithQueue;
    /**
     * Create the event listener.
     */

    //public $afterCommit = true;

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        SendValidationEmail::dispatch($event->user->id);
    }
}
