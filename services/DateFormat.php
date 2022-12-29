<?php

namespace app\services;

use DateTime;

class DateFormat
{
    /**
     * Формат даты для \DateTime
     * ex: (new \DateTime())->format(Constants::DATE_TIME) // 2018-05-31 12:25:37
     */
    public const DATE_TIME = 'Y-m-d H:i:s';

    /**
     * Формат даты для \DateTime
     * ex: (new \DateTime())->format(Constants::DATE_TIME) // 2018-05-31 12:25
     */
    public const DATE_TIME_SM = 'Y-m-d H:i';

    /**
     * Формат даты для \DateTime
     * ex: (new \DateTime())->format(Constants::DATE) // 2018-05-31
     */
    public const DATE = 'Y-m-d';

    public static function datetime(DateTime $dateTime): string
    {
        return $dateTime->format(self::DATE_TIME);
    }

    public static function datetimeShort(DateTime $dateTime): string
    {
        return $dateTime->format(self::DATE_TIME_SM);
    }

    public static function date(DateTime $dateTime): string
    {
        return $dateTime->format(self::DATE);
    }
}