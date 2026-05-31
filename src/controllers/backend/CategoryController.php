<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\controllers\backend;

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\forms\backend\CategoryForm;
use Besnovatyj\TreeManager\Manager\controllers\TreeController;
use Besnovatyj\TreeManager\Manager\services\TreeServiceInterface;
use Besnovatyj\TreeManager\Manager\TreeDataSource;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class CategoryController extends TreeController
{
    /**
     * @throws NotInstantiableException
     * @throws InvalidConfigException
     */
    public function __construct($id, $module, $config = [])
    {
        /** @var $treeManager TreeServiceInterface */
        $treeManager = Yii::$container->get('gallery.tree.manager');
        $this->treeManager = $treeManager;
        $this->dataSource = new TreeDataSource(
            Category::class,
            function (Category $model) {
                return [
                    'id' => $model->id,
                    'title' => $model->name,
                    'slug' => $model->slug,
                ];
            },
            'sort_order'
        );
        $this->createFormClass = CategoryForm::class;
        $this->updateFormClass = CategoryForm::class;
        $this->formView = '_form';
        $this->indexTitle = 'Управление категориями';
        parent::__construct($id, $module, $config);
    }
}
