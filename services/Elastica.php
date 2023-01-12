<?php

namespace app\services;

class Elastica
{
    public static function escapeQuery(string $sQuery): string
    {
        // экранируем поисковый запрос
        $sQuery = preg_replace_callback(
            "/[\\+\\-\\=\\&\\|\\!\\(\\)\\{\\}\\[\\]\\^\\\"\\~\\*\\<\\>\\?\\:\\\\\\/]/",
            function ($matches) {
                return "\\" . $matches[0];
            }, $sQuery
        );

        return $sQuery;
    }
}