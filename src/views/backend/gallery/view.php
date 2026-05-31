<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\helpers\GalleryHelper;
use Besnovatyj\Images\widgets\upload\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $gallery Gallery */
/* @var $absoluteFrontendUrl string */

$this->title = $gallery->name;
$this->params['breadcrumbs'][] = ['label' => 'Galleries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php if ($gallery->isActive()): ?>
        <?= Html::a('To draft', ['draft', 'id' => $gallery->id], ['class' => 'btn  btn-warning', 'data-method' => 'post']) ?>
    <?php else: ?>
        <?= Html::a('To active', ['activate', 'id' => $gallery->id], ['class' => 'btn  btn-success', 'data-method' => 'post']) ?>
    <?php endif; ?>
    <?= Html::a('Update', ['update', 'id' => $gallery->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $gallery->id], [
        'class' => 'btn  btn-danger',
        'data'  => [
            'confirm' => 'Are you sure?',
            'method'  => 'post',
        ],
    ]) ?>

    <a class="btn  btn-secondary" target="_blank"
       href="<?= $absoluteFrontendUrl; ?>">
        <i class="bi bi-eye"></i>
    </a>
</p>

<div class="container">
    <div class="row">
        <div class="col-sm">
            <!--COMMON-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Common</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-common" role="button"
                       aria-expanded="true" aria-controls="collapseCommon">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-common">
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model'      => $gallery,
                            'attributes' => [
                                'id',
                                [
                                    'attribute' => 'status',
                                    'value'     => GalleryHelper::statusLabel($gallery),
                                    'format'    => 'raw',
                                ],
                                'name',
                                [
                                    'attribute' => 'category_id',
                                    'value'     => ArrayHelper::getValue($gallery, 'category.name'),
                                ],
                                [
                                    'label' => 'Теги',
                                    'value' => implode(', ', ArrayHelper::getColumn($gallery->tags, 'name')),
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <!--DESCRIPTION-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Description</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-description" role="button"
                       aria-expanded="true" aria-controls="collapseDescription">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-description">
                    <div class="card-body">
                        <?= Yii::$app->formatter->asHtml($gallery->description, [
                            'Attr.AllowedRel'        => array('nofollow'),
                            'HTML.SafeObject'        => true,
                            'Output.FlashCompat'     => true,
                            'HTML.SafeIframe'        => true,
                            'URI.SafeIframeRegexp'   => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <!--SEO-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">SEO</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-SEO" role="button"
                       aria-expanded="true" aria-controls="collapseSEO">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-SEO">
                    <div class="card-body">
                        <?= DetailView::widget([
                            'model'      => $gallery,
                            'attributes' => [
                                [
                                    'attribute' => 'meta.title',
                                    'value'     => $gallery->meta->title,
                                ],
                                [
                                    'attribute' => 'meta.description',
                                    'value'     => $gallery->meta->description,
                                ],
                                [
                                    'attribute' => 'meta.keywords',
                                    'value'     => $gallery->meta->keywords,
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <!--IMAGES-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Images</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-images" role="button"
                       aria-expanded="true" aria-controls="collapseImages">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-images">
                    <div class="card-body">
                        <?= Widget::widget([
                            'ownerId'   => $gallery->id,
                            'endpoints' => [
                                'getImages'    => Url::to(['/Gallery/backend/gallery/get-images'], true),
                                'setNewSort'   => Url::to(['/Gallery/backend/gallery/set-new-sort'], true),
                                'upload'       => Url::to(['/Gallery/backend/gallery/add-image'], true),
                                'deleteImage'  => Url::to(['/Gallery/backend/gallery/delete-image'], true),
                                'setMainImage' => '/Gallery/backend/gallery/set-main-image',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
