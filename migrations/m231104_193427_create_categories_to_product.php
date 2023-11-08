<?php

use yii\db\Migration;

/**
 * Class m231104_193427_create_categories_to_product
 */
class m231104_193427_create_categories_to_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->createTable('categories_to_product', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer(100)->notNull(),
            'product_id' => $this->integer(100)->notNull(),
        ]);
      
        // creates index for column `category_id`
        $this->createIndex(
            'idx-category-id',
            'categories_to_product',
            'category_id'
        );
        
        // creates index for column `product_id`
        $this->createIndex(
            'idx-product-id',
            'categories_to_product',
            'product_id'
        );
        
        // add foreign key for table `categories`
        $this->addForeignKey(
            'fk-category',
            'categories_to_product',
            'category_id',
            'categories',
            'category_id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231104_193427_create_categories_to_product cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231104_193427_create_categories_to_product cannot be reverted.\n";

        return false;
    }
    */
}
