<?php

use App\Enums\Queue;

return [

    /*
    |--------------------------------------------------------------------------
    | AdministratorServices Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where AdministratorServices will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | AdministratorServices Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all AdministratorServices data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of AdministratorServices on the same server so that they don't have problems.
    |
    */

    'prefix' => env('HORIZON_PREFIX', 'horizon:'),

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    'waits' => [
        'redis:horizon' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire AdministratorServices to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    'trim' => [
        'recent' => 60,
        'failed' => 10080,
    ],


    'middleware' => [
        'web',
        \App\Http\Middleware\AuthenticateHorizon::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by AdministratorServices during deployment.
    |
    */

    'environments' => [
        'production' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => [Queue::DEFAULT],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 3,
            ],
            'supervisor-mail' => [
                'connection' => 'redis',
                'queue' => [Queue::MAIL],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 3,
            ],
            'supervisor-notifications' => [
                'connection' => 'redis',
                'queue' => [Queue::NOTIFICATION],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
            ],
            'supervisor-messages' => [
                'connection' => 'redis',
                'queue' => [Queue::MESSAGE],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
            ],
        ],

        'staging' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => [Queue::DEFAULT],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 3,
            ],
            'supervisor-mail' => [
                'connection' => 'redis',
                'queue' => [Queue::MAIL],
                'balance' => 'auto',
                'processes' => 5,
                'tries' => 3,
            ],
            'supervisor-notifications' => [
                'connection' => 'redis',
                'queue' => [Queue::NOTIFICATION],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
            ],
            'supervisor-messages' => [
                'connection' => 'redis',
                'queue' => [Queue::MESSAGE],
                'balance' => 'auto',
                'processes' => 10,
                'tries' => 3,
            ],
        ],

        'local' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => [Queue::DEFAULT],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
            'supervisor-mail' => [
                'connection' => 'redis',
                'queue' => [Queue::MAIL],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
            'supervisor-notifications' => [
                'connection' => 'redis',
                'queue' => [Queue::NOTIFICATION],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
            'supervisor-messages' => [
                'connection' => 'redis',
                'queue' => [Queue::MESSAGE],
                'balance' => 'auto',
                'processes' => 3,
                'tries' => 3,
            ],
        ],
    ],
];
