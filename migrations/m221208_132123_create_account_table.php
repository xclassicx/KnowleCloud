<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `account`
 */
class m221208_132123_create_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): bool
    {
        $this->createTable('{{%' . Tables::ACCOUNT . '}}', [
            // Base
            'id'          => $this->primaryKey(),
            'email'       => $this->string(255)->notNull()->unique(),
            'password'    => $this->string(255)->notNull(),
            'auth_key'    => $this->string(255)->notNull()->unique(), // Сессионный ключик
            // Profile data
            'first_name'  => $this->string(255), // Имя
            'second_name' => $this->string(255), // Фамилия
            // Life cycle
            'created'     => $this->dateTime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            'confirmed'   => $this->dateTime(), // Дата подтверждения email
        ]);

        $this->createIndex('idx-' . Tables::ACCOUNT . '-email', Tables::ACCOUNT, 'email');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%' . Tables::ACCOUNT . '}}');
        return true;
    }
}
