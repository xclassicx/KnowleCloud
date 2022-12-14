<?php

namespace app\services;

use DateTime;
use DateTimeZone;

/**
 * Хэлпер для передачи дат из/в БД
 */
class DbDate
{
    const USER_TIMEZONE = 'Europe/Moscow';
    const DB_TIMEZONE = 'GMT';

    // Кэш для объектов часового пояса - чтоб не пересоздавать
    protected static DateTimeZone $userTimeZone;
    protected static DateTimeZone $dbTimeZone;

    /**
     * Часовой пояс пользователя. Поскольку храним только в гринвиче - нужно для нормального отображения.
     */
    public static function getUserTimezone(): DateTimeZone
    {
        if (!isset(self::$userTimeZone)) {
            self::$userTimeZone = new DateTimeZone(self::USER_TIMEZONE);
        }

        return self::$userTimeZone;
    }

    /**
     * Часовой пояс дат, получаемых от БД
     */
    public static function getDbTimezone(): DateTimeZone
    {
        if (self::$dbTimeZone === null) {
            self::$dbTimeZone = new DateTimeZone(self::DB_TIMEZONE);
        }

        return self::$dbTimeZone;
    }

    /**
     * В базе храним только время по гринвичу, или с явным указанием часового пояса(т.е. приводимое к гринвичу).
     * В принципе операции над датами с разными ЧП будут работать нормально: и сравнение, и сложение итд.
     * Возвращаем же в часовом поясе пользователя(на данный момент - только мск) - для явного отображения.
     */
    public static function fromDatabase(?string $sDateTime): ?DateTime
    {
        if ($sDateTime === null) {
            return null;
        }

        // Критично указать часовой пояс при создании даты. Иначе будет использоваться date_default_timezone_set() - мск
        // В результате будет именно НЕВЕРНОЕ время в \DateTime
        return (new DateTime($sDateTime, self::getDbTimezone()))
            ->setTimezone(self::getUserTimezone());
    }

    /**
     * Дата-время в формате, годном для сохранения в бд
     */
    public static function toDatabase(DateTime $dateTime): string
    {
        return $dateTime->setTimezone(self::getDbTimezone())
            ->format(DateTime::W3C);
    }
}