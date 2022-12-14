<?php

use yii\caching\FileCache;
use yii\console\controllers\MigrateController;
use yii\gii\Module as GiiModule;
use yii\log\FileTarget;

$db = require __DIR__ . '/db.php';

$config = [
    'id'                  => 'basic-console',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap'       => [
        'migrate' => [
            'class'               => MigrateController::class,
            'migrationPath'       => [
                //'@yii/rbac/migrations',
            ],
            'migrationNamespaces' => [
                'app\migrations',
            ],
        ],
    ],
    'components'          => [
        'cache' => [
            'class' => FileCache::class,
        ],
        'log'   => [
            'targets' => [
                [
                    'class'  => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'    => $db,
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => GiiModule::class,
    ];
}

return $config;
