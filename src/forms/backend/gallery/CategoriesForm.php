<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\forms\backend\gallery;

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\base\Model;

class CategoriesForm extends Model
{
    public int|null $main = null;

    public function __construct(?Gallery $gallery = null, $config = [])
    {
        if ($gallery) {
            $this->main = $gallery->category_id;
        }
        parent::__construct($config);
    }

    public function categoriesList(): array
    {
        $scope = new TreeQueryScope(Category::class);
        return $scope->dropdownTree();
    }

    public function rules(): array
    {
        return [
            ['main', 'required'],
            ['main', 'integer'],
        ];
    }
}
