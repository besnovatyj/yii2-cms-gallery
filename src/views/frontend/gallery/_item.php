<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\gallery\Gallery;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $model Gallery */

$url = Url::to(['gallery', 'id' => $model->id]);

?>

<a href="<?= Html::encode($url) ?>" class="card h-100 text-decoration-none shadow-sm overflow-hidden">
    <div class="ratio ratio-1x1">
        <img src="<?= Html::encode($model->mainImage->getThumbUrl('file', 'frontend_list')) ?>"
             class="card-img-top object-fit-cover" alt="<?= Html::encode($model->name) ?>">
    </div>
    <?php if ($model->name): ?>
        <div class="card-body text-center">
            <h2 class="h6 card-title mb-0 text-body"><?= Html::encode($model->name) ?></h2>
        </div>
    <?php endif; ?>
</a>
