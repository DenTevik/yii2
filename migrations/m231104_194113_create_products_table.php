<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%products}}`.
 */
class m231104_194113_create_products_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%products}}', [
            'product_id' => $this->primaryKey(),
            'product_title' => $this->string(255)->notNull(),
            'product_content' => $this->text(),
        ]);
                
         // add foreign key for table `categories`
        $this->addForeignKey(
            'fk-product-to-product-category',
            'categories_to_product',
            'product_id',
            'products',
            'product_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%products}}');
    }
}
