<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\readModels;

use Besnovatyj\Contracts\search\SearchDocument;
use Besnovatyj\Contracts\sitemap\SitemapUrl;
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

    /**
     * Корневая категория дерева — только видимая: корень такая же полноценная категория, как
     * остальные, и снятая с публикации показываться не должна.
     *
     * Тип возврата стал nullable: скрытого (или отсутствующего) корня теперь может не быть, и
     * вызывающая сторона обязана это учитывать — раньше метод молча возвращал бы `null` вопреки
     * объявленному типу.
     */
    public function getRoot(): ?Category
    { // TODO - Что за метод? Теперь много корней деревьев
        return Category::find()->visible()->andWhere(['depth' => 0])->one();
    }

    /**
     * @return Category[]
     */
    public function getAll(): array
    {
        return Category::find()->visible()->orderBy('lft')->all();
    }

    public function find($id): ?Category
    {
        return Category::find()->visible()->andWhere(['id' => $id])->one();
    }

    /**
     * Категория по slug для фронтенда — только доступная анонимному посетителю: снятая с
     * публикации (или лежащая в скрытой ветке) не должна открываться по прямой ссылке.
     */
    public function findBySlug($slug): ?Category
    {
        return Category::find()->visible()->andWhere(['slug' => $slug])->one();
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
        $query = Category::find()->visible()->orderBy(['lft' => SORT_ASC]);
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


    /**
     * Видимые категории галереи для карты сайта.
     *
     * Обход в порядке дерева (`tree`, `lft`) и глубина узла отдаются как есть: отступ на
     * человеческой карте — забота представления, а не провайдера.
     *
     * Отпечатка свежести у категорий нет: колонок времени в дереве не заведено. Категорий немного,
     * полный обход дёшев.
     *
     * @return iterable<SitemapUrl>
     */
    public function sitemapUrls(): iterable
    {
        $query = Category::find()->visible()->orderBy(['tree' => SORT_ASC, 'lft' => SORT_ASC]);

        /** @var Category $category */
        foreach ($query->each(200) as $category) {
            yield new SitemapUrl(
                route: '/Gallery/gallery/category',
                params: ['slug' => $category->slug],
                title: (string)$category->name,
                depth: (int)$category->depth,
            );
        }
    }
}
