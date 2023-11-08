<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\bootstrap5\Html;

/**
 * Rest is the model behind the Rest Api Controller.
 */
class Rest extends Model
{
    /**
     * Summary of getCategories. 
     * @return mixed
     */
    public static function getCategories()
    {
        $all_categories = self::getAllCategories();

        if (empty($all_categories))
            return ['error' => true, 'text' => 'Categories not found'];
        else
            return self::CategoriesWalker($all_categories);

    }

    /**
     * Summary of addCategories
     * @throws \yii\web\BadRequestHttpException
     * @return array
     */
    public static function addCategories()
    {
        $category_name = $check_category_parent = null;
        $category_parent = 0;

        extract(Yii::$app->getRequest()->getBodyParams());

        $model = new DynamicModel(['category_name' => $category_name]);
        $model->addRule(['category_name'], 'string', ['min' => 1, 'skipOnEmpty' => false])
            ->validate();

        if ($model->hasErrors()) {
            throw new BadRequestHttpException(json_encode($model->errors));
        } else {

            if ($category_parent > 0) {
                $check_category_parent = Yii::$app->db->createCommand(
                    "SELECT category_id 
                    FROM categories 
                    WHERE category_id = '$category_parent'"
                )->queryScalar();

                if ($check_category_parent === false)
                    return ['error' => true, 'text' => 'Not exists category_parent = ' . $category_parent];

            }

            $check_category_name = Yii::$app->db->createCommand(
                "SELECT category_id 
                FROM categories 
                WHERE category_title LIKE '$category_name'
                AND category_parent = '" . ((!is_null($category_parent)) ? $category_parent : 0) . "'"
            )->queryScalar();

            if ($check_category_name === false) {

                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();

                try {
                    Yii::$app->db->createCommand()->insert('categories', [
                        'category_title' => $category_name,
                        'category_parent' => (!is_null($category_parent)) ? $category_parent : 0,
                    ])->execute();
                    $last_category_id = Yii::$app->db->getLastInsertId();
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                return ['error' => false, 'text' => 'Category successfull added - category_id = ' . $last_category_id];

            } else {

                return ['error' => true, 'text' => 'Category name exists for category_parent = ' . $category_parent];

            }
        }
    }

    /**
     * Summary of updateCategory
     * @throws \yii\web\BadRequestHttpException
     * @return array
     */
    public static function updateCategory()
    {
        $category_name = $category_id = null;
        $category_parent = 0;

        extract(Yii::$app->getRequest()->getBodyParams());

        $model = new DynamicModel(['category_name' => $category_name, 'category_id' => $category_id]);
        $model->addRule(['category_name'], 'string', ['min' => 1, 'skipOnEmpty' => false])
            ->addRule(['category_id'], 'integer', ['min' => 1, 'skipOnEmpty' => false])
            ->validate();

        if ($model->hasErrors()) {
            throw new BadRequestHttpException(json_encode($model->errors));
        } else {

            if ($category_parent > 0) {
                $check_category_parent = Yii::$app->db->createCommand(
                    "SELECT category_id 
                    FROM categories 
                    WHERE category_id = '$category_parent'"
                )->queryScalar();

                if ($check_category_parent === false)
                    return ['error' => true, 'text' => 'Not exists category_parent = ' . $category_parent];
            }

            $check_category = Yii::$app->db->createCommand(
                "SELECT * 
                FROM categories 
                WHERE category_id = '$category_id'"
            )->queryOne();
            if (!empty($check_category)) {

                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();

                try {
                    Yii::$app->db->createCommand()->update('categories', [
                        'category_title' => $category_name,
                        'category_parent' => (!is_null($category_parent)) ? $category_parent : $check_category['category_parent'],
                    ], 'category_id = ' . $check_category['category_id'])->execute();
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                return ['error' => false, 'text' => 'Category successfull updated - category_id = ' . $check_category['category_id']];

            } else {

                return ['error' => true, 'text' => 'Category with category_id = ' . $category_id . ' not found'];

            }
        }
    }

    /**
     * Summary of deleteCategory
     * @throws \yii\web\BadRequestHttpException
     * @return array
     */
    public static function deleteCategory()
    {
        $category_id = null;

        extract(Yii::$app->getRequest()->getBodyParams());

        $model = new DynamicModel(['category_id' => $category_id]);
        $model->addRule(['category_id'], 'integer', ['min' => 1, 'skipOnEmpty' => false])
            ->validate();

        if ($model->hasErrors()) {
            throw new BadRequestHttpException(json_encode($model->errors));
        } else {

            $check_category = Yii::$app->db->createCommand(
                "SELECT category_id
                FROM categories 
                WHERE category_id = '$category_id'"
            )->queryOne();

            if (!empty($check_category)) {

                $all_categories = self::getAllCategories();
                $childs_id = self::getCategoriesChilds($all_categories, $check_category['category_id']);
                $delete_ids = array_merge($childs_id, [$check_category['category_id']]);
                $delete_ids = implode(',', $delete_ids);

                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();

                try {
                    Yii::$app->db->createCommand()->delete('categories', 'category_id IN (' . $delete_ids . ')')->execute();
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                return ['error' => false, 'text' => 'Category with category_id = ' . $check_category['category_id'] . ' successfull deleted (with subcategory)'];

            } else {

                return ['error' => true, 'text' => 'Category with category_id = ' . $category_id . ' not found'];

            }
        }
    }

    /**
     * Summary of getAllCategories. Get all categories presents in DB.
     * @return array
     */
    public static function getAllCategories()
    {
        $categories_sort = [];
        $categories = Yii::$app->db->createCommand(
            "SELECT * 
            FROM categories
            ORDER BY category_parent ASC, category_title ASC"
        )->queryAll();
        foreach ($categories as $category) {
            $categories_sort[$category['category_parent']][] = $category;
        }

        return $categories_sort;

    }

    /**
     * Summary of CategoriesWalker. Walker method for building right tree array.
     * @param mixed $categories
     * @param mixed $parent
     * @return array
     */
    public static function CategoriesWalker($categories = [], $parent = 0)
    {
        if (empty($categories))
            throw new BadRequestHttpException('Categories not found');

        $categories_walker = [];
        foreach ($categories[$parent] as $category) {
            $categories_walker[$category['category_id']] = [];
            $categories_walker[$category['category_id']] = $category;
            if (key_exists($category['category_id'], $categories)) {
                $categories_walker[$category['category_id']]['childs'] = self::CategoriesWalker($categories, $category['category_id']);
            }
        }
        return $categories_walker;
    }

    /**
     * Summary of CategoriesWalkerHTML
     * @param mixed $categories
     * @param mixed $parent
     * @return string
     */
    public static function CategoriesWalkerHTML($categories = [], $parent = 0)
    {
        $categories_walker = '<ul>';
        if (!empty($categories)) {
            foreach ($categories[$parent] as $category) {
                $categories_walker .= '<li>';
                $categories_walker .= Html::a($category['category_title'], ['/catalog/' . $category['category_id']], ['class' => 'link']);
                if (key_exists($category['category_id'], $categories)) {
                    $categories_walker .= '<ul>';
                    $categories_walker .= self::CategoriesWalkerHTML($categories, $category['category_id']);
                    $categories_walker .= '</ul>';
                }
                $categories_walker .= '</li>';
            }
        } else {
            $categories_walker .= '<li>Категории не найдены!</li>';
        }
        $categories_walker .= '</ul>';
        return $categories_walker;
    }

    public static function printProductsHTML($category_id = null)
    {
        $products_out = $products_list = '';
        $products_not_found = '<p>Продукты не найдены!</p>';

        if ($category_id === null) {
            $products_out = Yii::$app->db->createCommand(
                "SELECT p.* 
                FROM products p
                INNER JOIN categories_to_product ctp ON (ctp.product_id = p.product_id)
                INNER JOIN categories c ON (ctp.category_id = c.category_id)
                WHERE c.category_parent = '0'
                ORDER BY product_title ASC"
            )->queryAll();
        } else {
            $products_out = Yii::$app->db->createCommand(
                "SELECT p.* 
                FROM products p
                INNER JOIN categories_to_product ctp ON (ctp.product_id = p.product_id)
                INNER JOIN categories c ON (ctp.category_id = c.category_id)
                WHERE c.category_id = '$category_id'
                ORDER BY product_title ASC"
            )->queryAll();
        }

        if (!empty($products_out)) {
            $products = '<ul>';
            foreach ($products_out as $product) {
                $products_list .= '<li>';
                $products_list .= '<h3>' . $product['product_title'] . '</h3>';
                $products_list .= '<p>' . $product['product_content'] . '</p>';
                $products_list .= '</li>';
            }
            $products .= $products_list;
            $products .= '</ul>';
        } else {
            $products = $products_not_found;
        }

        return $products;
    }

    /**
     * Summary of getCategoriesChilds. Get all childs for some parent, Only if parent_id > 0.
     * @param mixed $categories
     * @param mixed $parent
     * @throws \yii\web\BadRequestHttpException
     * @return array
     */
    public static function getCategoriesChilds($categories = [], $parent = 0)
    {
        static $categories_walker = [];

        if (empty($parent))
            throw new BadRequestHttpException('Not valid category_id value');

        if (empty($categories[$parent]))
            return $categories_walker;

        foreach ($categories[$parent] as $category) {
            $categories_walker[] = $category['category_id'];
            if (key_exists($category['category_id'], $categories)) {
                self::getCategoriesChilds($categories, $category['category_id']);
            }
        }

        return $categories_walker;
    }

    /**
     * Summary of getCategories. 
     * @return array
     */
    public static function getProducts()
    {
        $all_products = self::getAllProducts();

        if (empty($all_products))
            return ['error' => true, 'text' => 'Products not found'];
        else
            return $all_products;

    }

    /**
     * Summary of addProduct. 
     * @return array
     */
    public static function addProduct()
    {
        $product_name = $product_content = $product_category_id = $check_product_category = null;

        extract(Yii::$app->getRequest()->getBodyParams());

        $model = new DynamicModel(['product_name' => $product_name, 'product_content' => $product_content, 'product_category_id' => $product_category_id]);
        $model->addRule(['product_name', 'product_content'], 'string', ['min' => 1, 'skipOnEmpty' => false])
            ->addRule(['product_category_id'], 'string', ['min' => 1, 'skipOnEmpty' => false])
            ->validate();

        if ($model->hasErrors()) {
            throw new BadRequestHttpException(json_encode($model->errors));
        } else {
            $check_product_category_array = explode(',', $product_category_id);
            $check_product_category = Yii::$app->db->createCommand(
                "SELECT category_id 
                    FROM categories 
                    WHERE category_id IN ($product_category_id)"
            )->queryAll();

            if (empty($check_product_category))
                return ['error' => true, 'text' => 'Not exists product category = ' . $product_category_id];

            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();

            try {
                //create new product
                Yii::$app->db->createCommand()->insert('products', [
                    'product_title' => $product_name,
                    'product_content' => $product_content
                ])->execute();
                $last_product_id = Yii::$app->db->getLastInsertId();

                $batch_prepare = self::makeArrayForBatchInsert([
                    'categories' => $check_product_category,
                    'product_id' => $last_product_id,
                ]);

                $category_exists_array = $batch_prepare['category_exists_array'];

                $not_found_category = '';
                $categories_diff = array_diff($check_product_category_array, $category_exists_array);
                if (!empty($categories_diff))
                    $not_found_category = ', not found categories is = ' . implode(',', $categories_diff);

                //create link product to category
                Yii::$app->db->createCommand()->batchInsert(
                    'categories_to_product',
                    ['category_id', 'product_id'],
                    $batch_prepare['category_prepare']
                )->execute();

                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }

            return ['error' => false, 'text' => 'Produt successfull added - product_id = ' . $last_product_id . ' to categories = ' . implode(',', $category_exists_array) . $not_found_category];

        }
    }

    /**
     * Summary of updateProduct. 
     * @return array
     */
    public static function updateProduct()
    {
        $product_id = $product_name = $product_content = $product_category_id = $check_product_category = null;

        extract(Yii::$app->getRequest()->getBodyParams());

        $model = new DynamicModel(['product_name' => $product_name, 'product_content' => $product_content, 'product_category_id' => $product_category_id, 'product_id' => $product_id]);
        $model->addRule(['product_name', 'product_content'], 'string', ['min' => 1, 'skipOnEmpty' => true])
            ->addRule(['product_category_id'], 'string', ['min' => 1, 'skipOnEmpty' => true])
            ->addRule(['product_id'], 'integer', ['min' => 1, 'skipOnEmpty' => false])
            ->validate();

        if (is_null($product_name) && is_null($product_content) && is_null($product_category_id))
            throw new BadRequestHttpException('Nothing to update :( add product_name or product_content');

        if ($model->hasErrors()) {
            throw new BadRequestHttpException(json_encode($model->errors));
        } else {

            $check_product = Yii::$app->db->createCommand(
                "SELECT * 
                FROM products 
                WHERE product_id = '$product_id'"
            )->queryOne();

            if (!empty($check_product)) {

                if (!is_null($product_category_id)) {
                    $check_product_category_array = explode(',', $product_category_id);
                    $check_product_category = Yii::$app->db->createCommand(
                        "SELECT category_id 
                    FROM categories 
                    WHERE category_id IN ($product_category_id)"
                    )->queryAll();

                    if (empty($check_product_category))
                        return ['error' => true, 'text' => 'Not exists product category = ' . $product_category_id];
                }

                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();

                try {
                    //update product
                    if (!is_null($product_name) || !is_null($product_content)) {
                        Yii::$app->db->createCommand()->update('products', [
                            'product_title' => (!is_null($product_name)) ? $product_name : $check_product['product_title'],
                            'product_content' => (!is_null($product_content)) ? $product_content : $check_product['product_content'],
                        ], 'product_id = ' . $check_product['product_id'])->execute();
                    }

                    $not_found_category = $text_category_exists_array = '';
                    if (!is_null($product_category_id)) {

                        $batch_prepare = self::makeArrayForBatchInsert([
                            'categories' => $check_product_category,
                            'product_id' => $check_product['product_id'],
                        ]);

                        $category_exists_array = $batch_prepare['category_exists_array'];
                        $categories_diff = array_diff($check_product_category_array, $category_exists_array);

                        if (!empty($categories_diff))
                            $not_found_category = ', not found categories is = ' . implode(',', $categories_diff);

                        $text_category_exists_array = ' to categories = ' . implode(',', $category_exists_array);

                        //delete all link in categories_to_product table
                        Yii::$app->db->createCommand()->delete('categories_to_product', 'product_id = ' . $check_product['product_id'])->execute();

                        //create new link in categories_to_product table
                        Yii::$app->db->createCommand()->batchInsert(
                            'categories_to_product',
                            ['category_id', 'product_id'],
                            $batch_prepare['category_prepare']
                        )->execute();

                    }

                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                return ['error' => false, 'text' => 'Produt successfull updated - product_id = ' . $check_product['product_id'] . $text_category_exists_array . $not_found_category];

            } else {

                return ['error' => true, 'text' => 'Produt with product_id = ' . $product_id . ' not found'];

            }
        }
    }

    /**
     * Summary of deleteProduct. 
     * @return array
     */
    public static function deleteProduct()
    {
        $product_id = null;

        extract(Yii::$app->getRequest()->getBodyParams());

        $model = new DynamicModel(['product_id' => $product_id]);
        $model->addRule(['product_id'], 'integer', ['min' => 1, 'skipOnEmpty' => false])
            ->validate();

        if ($model->hasErrors()) {
            throw new BadRequestHttpException(json_encode($model->errors));
        } else {

            $check_product = Yii::$app->db->createCommand(
                "SELECT product_id
                FROM products 
                WHERE product_id = '$product_id'"
            )->queryOne();

            if (!empty($check_product)) {

                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();

                try {
                    Yii::$app->db->createCommand()->delete('products', 'product_id = ' . $check_product['product_id'])->execute();
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                return ['error' => false, 'text' => 'Product with product_id = ' . $check_product['product_id'] . ' successfull deleted'];

            } else {

                return ['error' => true, 'text' => 'Product with product_id = ' . $product_id . ' not found'];

            }
        }

    }

    /**
     * Summary of makeArrayForBatchInsert
     * @param mixed $data
     * @return array
     */
    public static function makeArrayForBatchInsert($data)
    {
        $result = [];
        $result_array = [];
        extract($data);
        foreach ($categories as $category) {
            $result[] = [
                $category['category_id'],
                $product_id
            ];
            $result_array[] = $category['category_id'];
        }
        return ['category_prepare' => $result, 'category_exists_array' => $result_array];
    }

    /**
     * Summary of getAllProducts
     * @return array
     */
    public static function getAllProducts()
    {
        $products_sort = [];
        $products = Yii::$app->db->createCommand(
            "SELECT p.*, c.category_id, c.category_title 
            FROM products p
            LEFT JOIN categories_to_product ctp ON (p.product_id = ctp.product_id)
            LEFT JOIN categories c ON (c.category_id = ctp.category_id)
            ORDER BY p.product_title ASC"
        )->queryAll();

        foreach ($products as $product) {
            $products_sort[$product['product_id']]['product_id'] = $product['product_id'];
            $products_sort[$product['product_id']]['product_title'] = $product['product_title'];
            $products_sort[$product['product_id']]['product_content'] = $product['product_content'];
            if (!empty($product['category_id']))
                $products_sort[$product['product_id']]['categories'][] = ['category_id' => $product['category_id'], 'category_title' => $product['category_title']];
            else
                $products_sort[$product['product_id']]['categories'] = [];
        }

        return $products_sort;

    }

}
