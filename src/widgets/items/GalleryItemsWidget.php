<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\widgets\items;

use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\readModels\GalleryReadRepository;
use Yii;
use yii\base\Widget;

/**
 * Виджет возвращает массив объектов фотографий конкретной галереи без какого-либо форматирования,
 * в отличие от Besnovatyj\Gallery\widgets\GalleryWidget
 */
class GalleryItemsWidget extends Widget
{
    public string $galleryId;
    public array $images = [];
    public GalleryReadRepository $repo;

    public function __construct(GalleryReadRepository $repo, $config = [])
    {
        parent::__construct($config);
        $this->repo = $repo;
    }

    public function init(): void
    {
        parent::init();
        if (!Yii::$app->getModule('Gallery')) {
            return;
        }
        $gallery = $this->repo->find($this->galleryId);
        if ($gallery instanceof Gallery) {
            $this->images = $gallery->images;
        }
    }

}
