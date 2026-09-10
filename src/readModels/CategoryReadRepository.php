<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\readModels;

use Besnovatyj\Contracts\search\SearchDocument;
use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\ArrayHelper;

class CategoryReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Category::class);
    }

    public function getRoot(): Category
    { // TODO - Что за метод? Теперь много корней деревьев
        return Category::find()->andWhere(['depth' => 0])->one();
    }

    /**
     * @return Category[]
     */
    public function getAll(): array
    {
        return Category::find()->orderBy('lft')->all();
    }

    public function find($id): ?Category
    {
        return Category::find()->andWhere(['id' => $id])->one();
    }

    public function findBySlug($slug): ?Category
    {
        return Category::find()->andWhere(['slug' => $slug])->one();
    }

    /**
     * Категории галерей для сквозного поиска — только видимые целиком, вместе с предками
     * ({@see \Besnovatyj\Gallery\entities\queries\CategoryQuery::visible()}).
     *
     * @return iterable<SearchDocument>
     */
    public function searchDocuments(): iterable
    {
        $query = Category::find()->visible()->orderBy(['id' => SORT_ASC]);

        /** @var Category $category */
        foreach ($query->each(100) as $category) {
            yield new SearchDocument(
                type: 'gallery.category',
                entityId: (int)$category->id,
                route: '/Gallery/gallery/category',
                params: ['slug' => $category->slug],
                title: (string)$category->name,
                text: (string)$category->description,
            );
        }
    }

    public function getTreeWithSubsOf(?Category $category = null): array
    {
        $query = Category::find()->andWhere(['status' => 1])->orderBy(['lft' => SORT_ASC]);
        if ($category) {
            $parents = $this->treeScope->parentsQuery($category)->all();

            $criteria = ['or', ['depth' => 1]];
            foreach (ArrayHelper::merge([$category], $parents) as $item) {
                $criteria[] = ['and', ['>', 'lft', $item->lft], ['<', 'rgt', $item->rgt], ['depth' => $item->depth + 1]];
            }
            $query->andWhere($criteria);
        } else {
            $query->andWhere(['depth' => 1]);
        }

        return $query->all();
    }
}
