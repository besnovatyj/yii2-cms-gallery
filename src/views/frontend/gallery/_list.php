<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use yii\bootstrap5\LinkPager;
use yii\data\DataProviderInterface;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider DataProviderInterface */

?>

<div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($dataProvider->getModels() as $model): ?>
        <div class="col">
            <?= $this->render('_item', [
                'model' => $model
            ]) ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-4 text-center">
    <?= LinkPager::widget([
        'pagination' => $dataProvider->getPagination(),
        'options' => [
            'class' => 'pagination',
        ],
        'linkOptions' => ['class' => 'page-link'],
    ]) ?>
</div>
