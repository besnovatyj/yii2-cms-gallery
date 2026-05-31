<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\forms\backend\gallery\GalleryForm;
use yii\web\View;

/* @var $this View */
/* @var $gallery Gallery */
/* @var $model GalleryForm */

$this->title = 'Update gallery: ' . $gallery->name;
$this->params['breadcrumbs'][] = ['label' => 'Galleries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $gallery->name, 'url' => ['view', 'id' => $gallery->id]];
$this->params['breadcrumbs'][] = 'Update';

echo $this->render('_form', [
    'model' => $model,
    'gallery' => $gallery,
]);
