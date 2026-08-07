<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\Tag;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider DataProviderInterface */
/* @var $tag Tag */

$this->title = 'Галереи с тегом: ' . $tag->name;

$this->params['og:title'] = $this->title;

$this->params['breadcrumbs'][] = ['label' => 'Галерея', 'url' => ['index']];
$this->params['breadcrumbs'][] = $tag->name;

$this->registerMetaTag(['name' => 'keywords', 'content' => Yii::$app->getModule('Config')->params['frontend']['app']['keywords']]);
$this->registerMetaTag(['name' => 'description', 'content' => Yii::$app->getModule('Config')->params['frontend']['app']['description']]);
$this->registerMetaTag(['name' => 'author', 'content' => Yii::$app->getModule('Config')->params['frontend']['app']['name']]);
?>

<section class="container mt-3 mb-5">
    <h1>Галереи с тегом: &laquo;<?= Html::encode($tag->name) ?>&raquo;</h1>
    <hr/>
    <?= $this->render('_list', [
        'dataProvider' => $dataProvider
    ]) ?>
</section>
