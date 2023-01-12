<?php

use app\models\Account;
use app\services\Route;
use yii\caching\FileCache;
use yii\debug\Module;
use yii\gii\Module as GiiModule;
use yii\log\FileTarget;
use yii\swiftmailer\Mailer;
use yii\web\UrlManager;
use yii\web\UrlNormalizer;
use yii\web\User;

$cache = require __DIR__ . '/components/cache.php';
$log = require __DIR__ . '/components/log.php';
$elasticsearch = require __DIR__ . '/components/elasticsearch.php';
$db = require __DIR__ . '/components/db.php';

$config = [
    'id'         => 'basic',
    'name'       => 'knowlecloud',
    'language'   => 'ru-RU',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request'       => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Wb=%zsL=jeu.7wKbDPf7T7@3A*CYM:vVq5eDtarH?#)toFr>rw)t.sJCCFG7cfvd',
        ],
        'user'          => [
            'class'           => User::class,
            'identityClass'   => Account::class,
            'enableAutoLogin' => true,
            'identityCookie'  => ['name' => '_identity', 'httpOnly' => true],
            'loginUrl'        => [Route::LOGIN],
            'enableSession'   => true,
        ],
        'errorHandler'  => [
            'errorAction' => 'site/error',
        ],
        'mailer'        => [
            'class'         => Mailer::class,
            'messageConfig' => [
                'from'    => ['noreply@knowlecloud.ru' => 'knowlecloud'],
                'charset' => 'UTF-8',
            ],
            'transport'     => [
                'class'         => Swift_SmtpTransport::class,
                'host'          => 'localhost',
                'username'      => '',
                'password'      => '',
                'port'          => '1025', // Port 25 is a very common port too
                'constructArgs' => ['localhost', 1025, ''], // Swift_Transport_EsmtpTransport не умеет в стрикт - указываем аргументы явно(особенно - последний)
                //    'encryption' => 'tls', // It is often used, check your provider or mail server specs
            ],
        ],
        'urlManager'    => [
            'class'               => UrlManager::class,
            'baseUrl'             => '',
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => true,
            'normalizer'          => [
                'class'                  => UrlNormalizer::class,
                'collapseSlashes'        => true,
                'normalizeTrailingSlash' => true,
            ],
            'rules'               => Route::getRules(),
        ],
        'cache'         => $cache,
        'log'           => $log,
        'elasticsearch' => $elasticsearch,
        'db'            => $db,
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'      => Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '192.168.56.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => GiiModule::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '192.168.56.1'],
    ];
}

return $config;
