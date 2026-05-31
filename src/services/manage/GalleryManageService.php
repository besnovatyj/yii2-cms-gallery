<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Gallery\services\manage;

use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\entities\gallery\TagAssignment;
use Besnovatyj\Gallery\entities\Tag;
use Besnovatyj\Gallery\forms\backend\gallery\GalleryForm;
use Besnovatyj\Gallery\repositories\CategoryRepository;
use Besnovatyj\Gallery\repositories\GalleryRepository;
use Besnovatyj\Gallery\repositories\TagRepository;
use Besnovatyj\Meta\Meta;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\helpers\Inflector;

/**
 * Сервис управления галереями.
 *
 * Отвечает за CRUD галерей и управление тегами.
 * Логика загрузки/удаления изображений вынесена в standalone actions
 * через пакет besnovatyj/yii2-cms-images + GalleryImageOwner.
 */
class GalleryManageService
{
    private GalleryRepository $galleries;
    private CategoryRepository $categories;
    private TagRepository $tags;

    public function __construct(
        GalleryRepository  $galleries,
        CategoryRepository $categories,
        TagRepository      $tags,
    )
    {
        $this->galleries = $galleries;
        $this->categories = $categories;
        $this->tags = $tags;
    }

    /**
     * @param GalleryForm $form
     * @return Gallery
     * @throws Throwable
     */
    public function create(GalleryForm $form): Gallery
    {
        $category = $this->categories->get($form->categories->main);

        $gallery = Gallery::create(
            $form->name,
            $form->description,
            $category->id,
            $form->status,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->galleries->save($gallery);
            $this->assignTags($gallery, $form->tags->newTagsNames);
            $transaction->commit();
            return $gallery;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     * @param GalleryForm $form
     * @throws Throwable
     */
    public function edit(int $id, GalleryForm $form): void
    {
        $gallery = $this->galleries->get($id);
        $category = $this->categories->get($form->categories->main);

        $gallery->edit(
            $form->name,
            $form->description,
            $form->status,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );

        $gallery->changeMainCategory($category->id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->galleries->save($gallery);

            $this->revokeTags($gallery);
            $this->assignTags($gallery, $form->tags->newTagsNames);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function remove(int $id): void
    {
        $gallery = $this->galleries->get($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->revokeTags($gallery);
            $this->removeImages($gallery);

            $this->galleries->remove($gallery);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function activate(int $id): void
    {
        $gallery = $this->galleries->get($id);
        $gallery->activate();
        $this->galleries->save($gallery);
    }

    /**
     * @throws Exception
     */
    public function draft(int $id): void
    {
        $gallery = $this->galleries->get($id);
        $gallery->draft();
        $this->galleries->save($gallery);
    }

    // ==================== Private methods ====================

    /**
     * @throws Exception
     */
    private function assignTags(Gallery $gallery, array $tagNames): void
    {
        foreach ($tagNames as $tagName) {
            $slug = Inflector::slug($tagName);

            $tag = $this->tags->findBySlug($slug);
            if (!$tag) {
                $tag = Tag::create($tagName, $slug);
                $this->tags->save($tag);
            }

            $exists = TagAssignment::find()
                ->andWhere(['gallery_id' => $gallery->id, 'tag_id' => $tag->id])
                ->exists();

            if ($exists) {
                continue;
            }

            $assignment = new TagAssignment();
            $assignment->gallery_id = $gallery->id;
            $assignment->tag_id = $tag->id;

            if (!$assignment->save()) {
                throw new Exception('Failed to save tag assignment.');
            }
        }
    }

    /**
     * @param Gallery $gallery
     */
    private function revokeTags(Gallery $gallery): void
    {
        TagAssignment::deleteAll(['gallery_id' => $gallery->id]);
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    private function removeImages(Gallery $gallery): void
    {
        foreach ($gallery->images as $image) {
            $image->delete();
        }
    }

}
