<?php

namespace app\services;

use app\models\Account;

/**
 * Хэлпер для работы с авторизованным пользователем
 */
class WebUser
{
    /**
     * Синтаксический сахар: Yii::$app->user->getIdentity() возвращает IdentityInterface|null
     * Этот метод возвращает - наш родной Account. Или null
     */
    public static function getAuthUser(): ?Account
    {
        if (\Yii::$app->user->getIsGuest()) {
            return null;
        }

        try {
            /** @var Account $mAccount */
            $mAccount = \Yii::$app->user->getIdentity();
        } catch (\Throwable $ex) {
            \Yii::error($ex);
            return null;
        }

        return $mAccount;
    }

    /**
     * Проверка разрешений текущего авторизованного пользователя
     */
    public static function can(string $permissionName, array $params = [], bool $allowCaching = true): bool
    {
        return \Yii::$app->user->can($permissionName, $params, $allowCaching);
    }
}