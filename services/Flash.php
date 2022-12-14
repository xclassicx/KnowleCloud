<?php

namespace app\services;

/**
 * Сессионные флеш сообщения
 */
class Flash
{
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';

    public static function addSuccess(string $sMessage): void
    {
        self::add(self::SUCCESS, $sMessage);
    }

    public static function addInfo(string $sMessage): void
    {
        self::add(self::INFO, $sMessage);
    }

    public static function addWarning(string $sMessage): void
    {
        self::add(self::WARNING, $sMessage);
    }

    public static function addDanger(string $sMessage): void
    {
        self::add(self::DANGER, $sMessage);
    }

    public static function add(string $sType, string $sMessage): void
    {
        \Yii::$app->session->addFlash($sType, $sMessage);
    }
}