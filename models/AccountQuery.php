<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Account]].
 *
 * @see Account
 */
class AccountQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return Account[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Account|array|null
     */
    public function one($db = null): mixed
    {
        return parent::one($db);
    }

    public function whereEmail(string $sEmail): static
    {
        return $this->andWhere('[[email]] = :email', [':email' => $sEmail]);
    }

    public function confirmed(): static
    {
        return $this->andWhere('[[confirmed]] IS NOT NULL');
    }
}