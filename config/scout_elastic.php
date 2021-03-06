<?php declare(strict_types = 1);

return [
    'client' => [
        'hosts' => [
            env('SCOUT_ELASTIC_HOST', 'exosuite-elasticsearch:9200'),
        ],
    ],
    'update_mapping' => env('SCOUT_ELASTIC_UPDATE_MAPPING', true),
    'indexer' => env('SCOUT_ELASTIC_INDEXER', 'single'),
    'document_refresh' => env('SCOUT_ELASTIC_DOCUMENT_REFRESH', 'wait_for'),
];
