<?php

use yii\log\FileTarget;

return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets'    => [
        [
            'class'  => FileTarget::class,
            'levels' => ['error', 'warning'],
        ],
    ],
];