<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\entities\gallery;

use Besnovatyj\Helpers\FilesystemHelper;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\entities\gallery\queries\GalleryQuery;
use Besnovatyj\Gallery\entities\Tag;
use Besnovatyj\PessimisticLock\PessimisticLockBehavior;
use DateTimeImmutable;
use DomainException;
use Besnovatyj\DomainEvents\AggregateRoot;
use Besnovatyj\Meta\Meta;
use Besnovatyj\DomainEvents\EventTrait;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $name
 * @property string $description
 * @property integer $category_id
 * @property integer $main_image_id
 * @property integer $status
 *
 * @property Meta $meta
 * @property Category $category
 * @property TagAssignment[] $tagAssignments
 * @property Tag[] $tags
 * @property Image[] $images
 * @property Image $mainImage
 *
 * @mixin PessimisticLockBehavior
 */
class Gallery extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    public const int STATUS_DRAFT = 0;
    public const int STATUS_ACTIVE = 1;

    public Meta $meta;

    public static function create($name, $description, $categoryId, $status, Meta $meta): self
    {
        $gallery = new static();
        $gallery->name = $name;
        $gallery->description = $description;
        $gallery->category_id = $categoryId;
        $gallery->status = $status;
        $gallery->created_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
        $gallery->meta = $meta;
        return $gallery;
    }

    public function edit($name, $description, $status, Meta $meta): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->status = $status;
        $this->meta = $meta;
        $this->updated_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
    }

    public function changeMainCategory($categoryId): void
    {
        $this->category_id = $categoryId;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Already enabled.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    public function draft(): void
    {
        if ($this->isDraft()) {
            throw new DomainException('Already disabled.');
        }
        $this->status = self::STATUS_DRAFT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    // <editor-fold desc="Images">

    public function setMainImage(?int $imageId): void
    {
        $this->main_image_id = $imageId;
    }

    // </editor-fold>

    // <editor-fold desc="Relations">

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getTagAssignments(): ActiveQuery
    {
        return $this->hasMany(TagAssignment::class, ['gallery_id' => 'id']);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagAssignments');
    }

    public function getImages(): ActiveQuery
    {
        return $this->hasMany(Image::class, ['gallery_id' => 'id'])->orderBy('sort');
    }

    public function getMainImage(): ActiveQuery
    {
        return $this->hasOne(Image::class, ['id' => 'main_image_id']);
    }

    // </editor-fold>

    // <editor-fold desc="Events">

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            if ($this->images) {
                foreach ($this->images as $image) {
                    $image->delete();
                }
            }

            $origin = Yii::getAlias('@static/origin/Gallery') . '/' . $this->id;
            $cache = Yii::getAlias('@static/cache/Gallery') . '/' . $this->id;
            FilesystemHelper::deleteDirContents($origin, true);
            FilesystemHelper::deleteDirContents($cache, true);

            return true;
        }
        return false;
    }

    // </editor-fold>

    public static function tableName(): string
    {
        return '{{%gallery_galleries}}';
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            PessimisticLockBehavior::class,
            ...parent::behaviors(),
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find(): GalleryQuery
    {
        return new GalleryQuery(static::class);
    }
}
