<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%categories}}`.
 */
class m231104_192648_create_categories_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%categories}}', [
            'category_id' => $this->primaryKey(),
            'category_parent' => $this->integer(100)->defaultValue(0),
            'category_title' => $this->string(255)->notNull(),
        ]);
        
        // creates index for column `category_parent`
        $this->createIndex(
            'idx-categories-parent',
            'categories',
            'category_parent'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%categories}}');
    }
}
