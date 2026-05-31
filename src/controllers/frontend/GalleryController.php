<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\controllers\frontend;

use Besnovatyj\Gallery\readModels\CategoryReadRepository;
use Besnovatyj\Gallery\readModels\GalleryReadRepository;
use Besnovatyj\Gallery\readModels\TagReadRepository;

use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GalleryController extends Controller
{
    private GalleryReadRepository $galleries;
    private CategoryReadRepository $categories;
    private TagReadRepository $tags;

    public function __construct(
        $id,
        $module,
        GalleryReadRepository $galleries,
        CategoryReadRepository $categories,
        TagReadRepository $tags,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->galleries = $galleries;
        $this->categories = $categories;
        $this->tags = $tags;
    }

    public function actionIndex(): string
    { // TODO - Что за метод? Теперь много корней деревьев
        $dataProvider = $this->galleries->getAll();
        $category = $this->categories->getRoot();

        return $this->render('/frontend/Gallery/index', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory(int $id): string
    {
        if (!$category = $this->categories->find($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $dataProvider = $this->galleries->getAllByCategory($category);

        return $this->render('/frontend/Gallery/category', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTag(int $id): string
    {
        if (!$tag = $this->tags->find($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $dataProvider = $this->galleries->getAllByTag($tag);

        return $this->render('/frontend/Gallery/tag', [
            'tag' => $tag,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGallery(int $id): string
    {
        if (!$gallery = $this->galleries->find($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('/frontend/Gallery/gallery', [
            'gallery' => $gallery,
        ]);
    }
}
