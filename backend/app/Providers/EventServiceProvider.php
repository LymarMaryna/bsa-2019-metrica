<?php

namespace App\Providers;

use App\Listeners\CreateVisitAggregate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\VisitCreated' => [
            'App\Listeners\SendVisitsNotification',
            CreateVisitAggregate::class,
        ],
        'App\Events\SessionCreated' => [
            'App\Listeners\SendSessionNotification'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
