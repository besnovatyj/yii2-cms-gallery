<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\forms\backend\gallery\GalleryForm;
use yii\web\View;

/* @var $this View */
/* @var $model GalleryForm */

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => 'Galleries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_form', ['model' => $model]);
