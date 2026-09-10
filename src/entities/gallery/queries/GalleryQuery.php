<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\entities\gallery\queries;

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use yii\db\ActiveQuery;

class GalleryQuery extends ActiveQuery
{
    /**
     * @param null $alias
     * @return $this
     */
    public function active($alias = null): static
    {
        return $this->andWhere([
            ($alias ? $alias . '.' : '') . 'status' => Gallery::STATUS_ACTIVE,
        ]);
    }

    /**
     * Галерея доступна анонимному посетителю: опубликована сама И лежит в видимой категории.
     *
     * Одной публикации мало: скрытая категория не должна «протекать» на фронт своими галереями —
     * категория проверяется целиком, вместе с предками
     * (см. {@see \Besnovatyj\Gallery\entities\queries\CategoryQuery::visible()}).
     * Галерея без категории (`category_id` NULL) видна: скрывать её не за что.
     *
     * @param string|null $alias алиас таблицы галерей, если запрос строится с `alias()`
     */
    public function visible(?string $alias = null): static
    {
        $column = ($alias ? $alias . '.' : '') . 'category_id';

        return $this->active($alias)->andWhere([
            'or',
            [$column => null],
            [$column => Category::find()->visible()->select('id')],
        ]);
    }
}
