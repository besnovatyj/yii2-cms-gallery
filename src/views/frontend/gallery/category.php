<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\base\Module;
use yii\data\DataProviderInterface;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider DataProviderInterface */
/* @var $category Category */

$this->title = $category->name;

$this->params['og:title'] = $this->title;

$this->params['breadcrumbs'] = new TreeQueryScope(Category::class)->breadcrumbs($category, urlCallback: function ($item) use ($category) {
    if ($item->id !== $category->id) {
        return Url::to(['category', 'slug' => $item->slug]);
    }
    return false;
});

$this->registerMetaTag(['name' => 'title', 'content' => $category->getSeoTitle()]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $category->meta->keywords]);
$this->registerMetaTag(['name' => 'description', 'content' => $category->meta->description]);

if (Yii::$app->getModule('Config') instanceof Module) {
    $this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->getModule('Config')->params['frontend']['app']['name']]);
}
?>
<section class="container mt-3 mb-5">
    <?= $this->render('_subcategories', [
        'category' => $category
    ]) ?>
    <?php if (trim($category->description)): ?>
        <div class="mb-4">
            <?= Yii::$app->formatter->asHtml($category->description) ?>
        </div>
    <?php endif; ?>
    <?= $this->render('_list', [
        'dataProvider' => $dataProvider
    ]) ?>
</section>
