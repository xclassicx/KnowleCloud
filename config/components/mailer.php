<?php

use yii\swiftmailer\Mailer;

$config = [
    'class'         => Mailer::class,
    'messageConfig' => [
        'from'    => ['noreply@knowlecloud.ru' => 'knowlecloud'],
        'charset' => 'UTF-8',
    ],
];

if (YII_ENV_DEV) {
    $config['transport'] = [
        'class'         => Swift_SmtpTransport::class,
        'host'          => 'localhost',
        'username'      => '',
        'password'      => '',
        'port'          => '1025', // Port 25 is a very common port too
        'constructArgs' => ['localhost', 1025, ''], // Swift_Transport_EsmtpTransport не умеет в стрикт - указываем аргументы явно(особенно - последний)
        //    'encryption' => 'tls', // It is often used, check your provider or mail server specs
    ];
}

return $config;