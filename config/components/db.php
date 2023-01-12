<?php

use yii\db\Connection;

return [
    'class'        => Connection::class,
    'dsn'          => 'sqlite:' . realpath(dirname(__DIR__) . '/../db/db.sqlite'),
    'charset'      => 'utf8',
    'attributes'   => [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
    'on afterOpen' => function ($event) {
        $event->sender->createCommand("PRAGMA foreign_keys = ON;")->execute();
    },
];
