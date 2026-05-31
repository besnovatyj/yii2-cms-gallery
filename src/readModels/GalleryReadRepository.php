<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\readModels;

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\entities\Tag;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\Expression;

class GalleryReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Category::class);
    }

    public function count(): int
    {
        return Gallery::find()->active()->count();
    }

    public function getAllByRange(int $offset, int $limit): array
    {
        return Gallery::find()->alias('p')->active('p')->orderBy(['created_at' => SORT_ASC])->limit($limit)->offset($offset)->all();
    }

    public function getAllIterator(): iterable
    {
        return Gallery::find()->alias('p')->active('p')->with('mainImage')->each();
    }

    public function getAll(): DataProviderInterface
    {
        $query = Gallery::find()->alias('p')->active('p')->with('mainImage');
        return $this->getProvider($query);
    }

    public function getAllByCategory(Category $category): DataProviderInterface
    {
        $query = Gallery::find()->alias('p')->active('p')->with('mainImage', 'category');
        $ids = $this->treeScope->descendantIds($category, andSelf: true);
        $query->andWhere(['p.category_id' => $ids]);
        $query->groupBy('p.id');
        return $this->getProvider($query);
    }

    public function getAllByTag(Tag $tag): DataProviderInterface
    {
        $query = Gallery::find()->alias('p')->active('p')->with('mainImage');
        $query->joinWith(['tagAssignments ta'], false);
        $query->andWhere(['ta.tag_id' => $tag->id]);
        $query->groupBy('p.id');
        return $this->getProvider($query);
    }

    public function getRand($limit): array
    {
        return Gallery::find()->active()->orderBy(new Expression('rand()'))->limit($limit)->all();
    }

    public function find(int $id): ?Gallery
    {
        /** @var $gallery Gallery */
        $gallery = Gallery::find()->active()->andWhere(['id' => $id])->one();
        return $gallery;
    }

    private function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'created_at' => [
                        'asc' => ['p.created_at' => SORT_ASC],
                        'desc' => ['p.created_at' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['p.name' => SORT_ASC],
                        'desc' => ['p.name' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => [
                'pageSizeLimit' => [15, 100],
                'pageSize' => 12,
            ]
        ]);
    }
}
