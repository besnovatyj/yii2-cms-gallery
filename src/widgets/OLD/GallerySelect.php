<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\widgets\OLD;

use Besnovatyj\Gallery\readModels\GalleryReadRepository;
use yii\bootstrap5\InputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Виджет со списком категорий для сайдбара фронтэнда
 */
class GallerySelect extends InputWidget
{
    public $gallery_id;
    public $repo;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->repo = new GalleryReadRepository();
    }


    public function run()
    {
        echo '<label class="control-label">Галерея</label>';
        echo \Besnovatyj\Select2\Select2Widget::widget([
            'value' => Html::getAttributeValue($this->model, $this->attribute),
            'name' => Html::getInputName($this->model, $this->attribute),
            'data' => ['0' => 'Нет галереи'] + $this->getAllGalleries(),
            'options' => [
                'multiple' => false,
            ],
            'pluginOptions' => [
            ],
        ]);

    }

    private function getAllGalleries()
    {
        return ArrayHelper::map($this->repo->getAllByRange(0, 1000000000), 'id', 'name');
    }

}
