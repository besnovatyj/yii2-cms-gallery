<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\entities\gallery\queries;

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
}
