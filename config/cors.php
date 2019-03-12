<?php declare(strict_types = 1);

return [

	/*
	|--------------------------------------------------------------------------
	| Laravel CORS
	|--------------------------------------------------------------------------
	|
	| allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
	| to accept any value.
	|
	*/

    'supportsCredentials' => false,
    'allowedOrigins' => [
        'http://app.exosuite.local',
        'http://exosuite.local',
        'https://app.teamexosuite.cloud',
        'https://app.exosuite.fr',
        'https://confluence.teamexosuite.cloud',
    ],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0,

];
