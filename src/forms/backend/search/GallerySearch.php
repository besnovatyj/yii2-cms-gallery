<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\forms\backend\search;

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\helpers\GalleryHelper;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Besnovatyj\Gallery\entities\gallery\Gallery;

class GallerySearch extends Model
{
    public $id;
    public $name;
    public $category_id;
    public $status;

    public function rules(): array
    {
        return [
            [['category_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['id'], 'integer'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Gallery::find()->with('mainImage', 'category');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    public function categoriesList(): array
    {
        $scope = new TreeQueryScope(Category::class);
        return $scope->dropdownTree();
    }

    public function statusList(): array
    {
        return GalleryHelper::statusList();
    }
}
