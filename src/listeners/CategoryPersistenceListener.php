<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\listeners;

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\repositories\events\EntityPersisted;
use yii\caching\Cache;
use yii\caching\TagDependency;

class CategoryPersistenceListener
{
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function handle(EntityPersisted $event): void
    {
        if ($event->entity instanceof Category) {
            TagDependency::invalidate($this->cache, ['gallery_cat']);
        }
    }
}
