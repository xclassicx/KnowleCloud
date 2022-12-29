<?php

namespace app\migrations;

/**
 * Справочник используемых SQL таблиц
 */
class Tables
{
    /**
     * Тут хранятся пользователи
     * @see M221208132123CreateAccountTable::safeUp()
     */
    public const ACCOUNT = 'account';

    /**
     * Тут - загруженные документы
     * @see M221214182914CreateDocumentTable
     */
    public const DOCUMENT = 'document';
}