<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Editor\EditorWidget;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\forms\backend\gallery\GalleryForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $model GalleryForm */
/* @var $gallery Gallery */
?>
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-md-6">
            <!--MAIN-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Main</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-main" role="button"
                       aria-expanded="true" aria-controls="collapseMain">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse show" id="collapse-main">
                    <div class="card-body">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                        <?= $form->field($model, 'status')->dropDownList($model->statusList(), ['prompt' => 'Не выбрано', 'class' => 'custom-select']) ?>
                        <?= $form->field($model->categories, 'main')->dropDownList($model->categories->categoriesList(), ['prompt' => 'Не выбрано', 'class' => 'custom-select'])->label('Category') ?>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <!--COMMON-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">Common</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-common" role="button"
                       aria-expanded="false" aria-controls="collapseCommon">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse" id="collapse-common">
                    <div class="card-body">
                        <?= $form->field($model->tags, 'newTagsNames')->widget(  \Besnovatyj\Select2\Select2Widget::class, [
                            'endpoint' => Url::to(['/Gallery/backend/tag/search-endpoint'], true),
                            'options' => ['class' => ''],
                        ])->label('Tags') ?>
                        <?php
                        if (!isset($gallery)) {
                            echo '<div class="alert alert-danger" role="alert">Перед заполнением контента сохраните.</div>';
                        } else {
                            // TODO создавать папку при создании поста. При удалении удалять.
                            $editorConfig = [];
                            $editorConfig['language'] = 'ru';
                            $editorConfig['fmDefaultPath'] = '/static/origin/Gallery/' . $gallery->id;
                            echo $form->field($model, 'description')->widget( EditorWidget::class, $editorConfig);
                        }
                        ?>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--SEO-->
            <div class="card">
                <div class="card-header d-md-flex justify-content-md-between">
                    <div class="pt-1">SEO</div>
                    <a class="btn btn-sm collapse-button" data-bs-toggle="collapse" href="#collapse-SEO" role="button"
                       aria-expanded="false" aria-controls="collapseSEO">
                        <i class="bi bi-plus-lg"></i>
                        <i class="bi bi-dash-lg"></i>
                    </a>
                </div>
                <div class="collapse" id="collapse-SEO">
                    <div class="card-body">
                        <?= $form->field($model->meta, 'title')->textInput(['class' => 'form-control']) ?>
                        <?= $form->field($model->meta, 'description')->textarea(['rows' => 2, 'class' => 'form-control']) ?>
                        <?= $form->field($model->meta, 'keywords')->textInput(['class' => 'form-control']) ?>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
