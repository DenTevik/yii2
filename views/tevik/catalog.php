<?php

/** @var yii\web\View $this */

use app\models\Rest;
use yii\bootstrap5\Html;

$this->title = 'Catalog';
$this->params['breadcrumbs'][] = $this->title;

$all_categories = Rest::getAllCategories();
$menu = Rest::CategoriesWalkerHTML($all_categories);
?>
<div class="site-about">
    <h1>
        <?= Html::encode($this->title) ?>
    </h1>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-5 mb-3 tevik_menu">
                <h2>Menu</h2>
                <?= $menu ?>
            </div>
            <div class="col-lg-7 mb-3 tevik_product">
                <h2>Products</h2>
                <?= Rest::printProductsHTML($id) ?>
            </div>
        </div>

    </div>
</div>