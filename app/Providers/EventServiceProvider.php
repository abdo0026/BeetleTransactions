<?php

namespace App\Providers;


use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\Account\UserRegisterred;
use App\Listeners\Account\SendVerificationEmail;

use App\Events\Transactions\PaymentCreated;
use App\Events\Transactions\PaymentDeleted;
use App\Listeners\Transactions\CalculateTransactionPaidAmount;
use App\Listeners\Transactions\EvaluateTransactionStatus;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegisterred::class => [
            SendVerificationEmail::class,
        ],

        PaymentCreated::class => [
            CalculateTransactionPaidAmount::class,
            EvaluateTransactionStatus::class
        ],

        PaymentDeleted::class => [
            CalculateTransactionPaidAmount::class,
            EvaluateTransactionStatus::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
