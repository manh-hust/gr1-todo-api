<?php

namespace App\Listeners;

use App\Events\RegisterSuccess;
use App\Notifications\SendMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class SendEmailNotification implements ShouldQueue
{
    public $connection = 'database';

    public $queue = 'listeners';

    public $tries = 5;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\RegisterSuccess  $event
     * @return void
     */
    public function handle(RegisterSuccess $event)
    {
        $event->user->notify(new SendMail());
    }
}
