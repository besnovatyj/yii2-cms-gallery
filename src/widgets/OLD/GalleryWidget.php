<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\widgets\OLD;

use Besnovatyj\Gallery\entities\gallery\Gallery;
use yii\base\Widget;

/**
 * Виджет отформатированную галерею для базовой темы, в отличие от Besnovatyj\Gallery\widgets\GalleryItemsWidget
 */
class GalleryWidget extends Widget
{
    public $gallery_id;
    public $isThumb;
    private $_gallery = null;

    public function init(): void
    {
        parent::init();
        $this->_gallery = Gallery::find()->andWhere(['id' => (int)$this->gallery_id])->active()->one();
    }

    public function run(): string
    {
        if (!is_object($this->_gallery)) return '';
        return $this->render('gallery', [
            'gallery' => $this->_gallery,
            'isThumb' => $this->isThumb,
        ]);
    }
}
