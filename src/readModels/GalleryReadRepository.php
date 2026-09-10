<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\readModels;

use Besnovatyj\Contracts\search\SearchDocument;
use Besnovatyj\Contracts\sitemap\SitemapUrl;
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
        return Gallery::find()->visible()->count();
    }

    public function getAllByRange(int $offset, int $limit): array
    {
        return Gallery::find()->alias('p')->visible('p')->orderBy(['created_at' => SORT_ASC])->limit($limit)->offset($offset)->all();
    }

    public function getAllIterator(): iterable
    {
        return Gallery::find()->alias('p')->visible('p')->with('mainImage')->each();
    }

    public function getAll(): DataProviderInterface
    {
        $query = Gallery::find()->alias('p')->visible('p')->with('mainImage');
        return $this->getProvider($query);
    }

    public function getAllByCategory(Category $category): DataProviderInterface
    {
        $query = Gallery::find()->alias('p')->visible('p')->with('mainImage', 'category');
        $ids = $this->treeScope->descendantIds($category, andSelf: true);
        $query->andWhere(['p.category_id' => $ids]);
        $query->groupBy('p.id');
        return $this->getProvider($query);
    }

    public function getAllByTag(Tag $tag): DataProviderInterface
    {
        $query = Gallery::find()->alias('p')->visible('p')->with('mainImage');
        $query->joinWith(['tagAssignments ta'], false);
        $query->andWhere(['ta.tag_id' => $tag->id]);
        $query->groupBy('p.id');
        return $this->getProvider($query);
    }

    public function getRand($limit): array
    {
        return Gallery::find()->visible()->orderBy(new Expression('rand()'))->limit($limit)->all();
    }

    public function find(int $id): ?Gallery
    {
        /** @var $gallery Gallery */
        $gallery = Gallery::find()->visible()->andWhere(['id' => $id])->one();
        return $gallery;
    }

    /**
     * Галереи для сквозного поиска — только публично доступные ({@see GalleryQuery::visible()}).
     *
     * Генератор с чтением пачками: полная переиндексация не должна держать в памяти все галереи.
     * Поля отдаются СЫРЫМИ — нормализация текста едина для всех модулей и выполняется модулем поиска.
     *
     * @return iterable<SearchDocument>
     */
    public function searchDocuments(): iterable
    {
        $query = Gallery::find()->alias('p')->visible('p')
            ->with(['tags', 'category', 'mainImage'])
            ->orderBy(['p.id' => SORT_ASC]);

        /** @var Gallery $gallery */
        foreach ($query->each(100) as $gallery) {
            $keywords = array_map(static fn (Tag $tag): string => (string)$tag->name, $gallery->tags);

            if ($gallery->category !== null) {
                $keywords[] = (string)$gallery->category->name;
            }

            yield new SearchDocument(
                type: 'gallery.gallery',
                entityId: (int)$gallery->id,
                route: '/Gallery/gallery/gallery',
                params: ['id' => (int)$gallery->id],
                title: (string)$gallery->name,
                text: (string)$gallery->description,
                keywords: implode(' ', $keywords),
                // `created_at` — колонка DATETIME, а контракт ждёт Unix-timestamp: приведение
                // (int) молча дало бы год вместо даты (грабли, уже пойманные в блоге).
                date: $gallery->created_at === null ? null : (strtotime((string)$gallery->created_at) ?: null),
                image: $gallery->mainImage?->getThumbUrl('file', 'frontend_list'),
            );
        }
    }

    private function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
//                'defaultOrder' => ['id' => SORT_DESC],
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


    /**
     * Альбомы галереи для карты сайта.
     *
     * Тот же инвариант, что у поиска, — только публично доступное. Карте нужны название и дата
     * ИЗМЕНЕНИЯ альбома: добавили в него снимки — краулер обязан прийти заново.
     *
     * @return iterable<SitemapUrl>
     */
    public function sitemapUrls(): iterable
    {
        $query = Gallery::find()->alias('p')->visible('p')->orderBy(['p.id' => SORT_DESC]);

        /** @var Gallery $gallery */
        foreach ($query->each(200) as $gallery) {
            yield new SitemapUrl(
                route: '/Gallery/gallery/gallery',
                params: ['id' => (int)$gallery->id],
                title: (string)$gallery->name,
                // updated_at — колонка DATETIME, а контракт ждёт Unix-timestamp.
                lastModified: $gallery->updated_at === null
                    ? null
                    : (strtotime((string)$gallery->updated_at) ?: null),
            );
        }
    }

    /**
     * Отпечаток состояния альбомов для карты сайта: сколько их и когда правили последний раз.
     *
     * Одного `MAX(updated_at)` мало — он не замечает удаления альбома, а удалённая страница обязана
     * исчезнуть из карты. Пара «сколько + когда» это закрывает и стоит одного запроса.
     */
    public function sitemapRevision(): string
    {
        $row = Gallery::find()->alias('p')->visible('p')
            ->select(['total' => 'COUNT(*)', 'latest' => 'MAX(p.updated_at)'])
            ->asArray()
            ->one();

        return ((string)($row['total'] ?? '0')) . ':' . ((string)($row['latest'] ?? ''));
    }
}
