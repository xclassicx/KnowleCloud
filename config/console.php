<?php

use app\commands\ElasticaController;
use yii\console\controllers\MigrateController;
use yii\gii\Module as GiiModule;

$cache = require __DIR__ . '/components/cache.php';
$log = require __DIR__ . '/components/log.php';
$elasticsearch = require __DIR__ . '/components/elasticsearch.php';
$db = require __DIR__ . '/components/db.php';

$config = [
    'id'                  => 'basic-console',
    'name'                => 'knowlecloud',
    'language'            => 'ru-RU',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap'       => [
        'migrate'  => [
            'class'               => MigrateController::class,
            'migrationPath'       => [
                //'@yii/rbac/migrations',
            ],
            'migrationNamespaces' => [
                'app\migrations',
            ],
        ],
        'elastica' => [
            'class' => ElasticaController::class,
        ],
    ],
    'components'          => [
        'cache'         => $cache,
        'log'           => $log,
        'elasticsearch' => $elasticsearch,
        'db'            => $db,
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
