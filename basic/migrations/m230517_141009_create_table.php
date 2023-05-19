<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%}}`.
 */
class m230517_141009_create_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'login' => $this->string()->notNull(),
            'password' => $this->string()->notNull(),
            'name' => $this->string(),
            'last_name' => $this->string(),
            'access_token' => $this->string()
        ]);
        $this -> alterColumn('user','id', $this->integer().' AUTO_INCREMENT');
        $this->createTable('chat', [
            'id' => $this->primaryKey()->notNull()->unique(),
            'message' => $this->string()->notNull(),
            'user_from' => $this->integer()->notNull(),
            'user_to' => $this->integer()->notNull(),
        ]);
        $this -> alterColumn('chat','id', $this->integer().' AUTO_INCREMENT');
        $this->addForeignKey(
            'fk-user_from_chat',
            'chat',
            'user_from',
            'user',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-user_to_chat',
            'chat',
            'user_to',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_from_chat', 'chat');
        $this->dropForeignKey('fk-user_to_chat', 'chat');
        $this->dropTable('user');
        $this->dropTable('chat');
    }
}
