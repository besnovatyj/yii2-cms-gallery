<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\entities;

use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\Meta\Meta;
use Besnovatyj\TreeManager\Manager\entities\Node;
use yii\db\ActiveQuery;

/**
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $tree
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property integer $status
 * @property int $sort_order - Порядок сортировки корневых узлов
 *
 * @property Meta $meta
 *
 * @mixin MetaBehavior
 */
class Category extends Node
{
    public Meta $meta;

    public static function create($name, $slug, $description, Meta $meta): self
    {
        $category = new static();
        $category->name = $name;
        $category->slug = $slug;
        $category->description = $description;
        $category->meta = $meta;
        return $category;
    }

    public function edit($name, $slug, $description, Meta $meta): void
    {
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->meta = $meta;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    public function changeStatus(): void
    {
        $this->status = !$this->status;
    }

    public function getGalleries(): ActiveQuery
    {
        return $this->hasMany(Gallery::class, ['category_id' => 'id']);
    }

    public static function tableName(): string
    {
        return '{{%gallery_categories}}';
    }

    public function countPerformancesByCategory(): bool|int|string|null
    {
        return $this->getGalleries()->count();
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            ...parent::behaviors(),
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
}
