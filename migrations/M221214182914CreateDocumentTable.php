<?php

namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table document.
 */
class M221214182914CreateDocumentTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): bool
    {
        $this->createTable('{{%' . Tables::DOCUMENT . '}}', [
            'id'             => $this->primaryKey(),
            'owner'          => $this->integer()->notNull(),
            'name'           => $this->string(255)->notNull(),
            'keywords'       => $this->text()->notNull(),
            'filename'       => $this->string(32)->notNull(),
            'file_extension' => $this->string(16)->notNull(),
            'file_mime'      => $this->string(64),
            'created'        => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'public'         => $this->boolean()->notNull(),
            'CONSTRAINT "' . Tables::DOCUMENT . '-owner-fk" FOREIGN KEY (owner) REFERENCES ' . Tables::ACCOUNT . ' (id) ON DELETE SET NULL',
        ]);

        $this->createIndex('idx-' . Tables::DOCUMENT . '-owner', Tables::DOCUMENT, 'owner');
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%' . Tables::DOCUMENT . '}}');
        return true;
    }
}
