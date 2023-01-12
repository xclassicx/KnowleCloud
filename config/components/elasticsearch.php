<?php

use yii\elasticsearch\Connection;

return [
    'class'      => Connection::class,
    'nodes'      => [
        ['http_address' => '127.0.0.1:9200'],
        // configure more hosts if you have a cluster
    ],
    'dslVersion' => 7,
];