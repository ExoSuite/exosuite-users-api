<?php declare(strict_types = 1);

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, SparkPost and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    //    'google' => [
    //        'client_id' => env('GOOGLE_CLIENT_ID'),
    //        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    //        'redirect' => env('GOOGLE_REDIRECT_URI'),
    //    ],
    'twitter' => [
        'client_id' => 'xdi8Y359o6gxjpOWLDBCtZeUT',
        'client_secret' => 'LQzjtk1ji7htA0ed8kwqblx3lLQGVJDbvQDjC6Wkk9YsOimYgF',
        'redirect' => env("APP_URL") . "/auth/login/twitter/callback",
    ],
];
