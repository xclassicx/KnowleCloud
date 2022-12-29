<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Document]].
 *
 * @see Document
 */
class DocumentQuery extends ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return Document[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Document|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function forCatalog(): self
    {
        return $this->andWhere('public = true');
    }

    public function whereOwner(Account $user): self
    {
        return $this->andWhere('owner = :user_id', ['user_id' => $user->getId()]);
    }
}
