<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Gallery\image;

use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\entities\gallery\Image;
use Besnovatyj\Gallery\repositories\GalleryRepository;
use Besnovatyj\Images\base\BaseImage;
use Besnovatyj\Images\contracts\ImageOwnerInterface;
use yii\db\Exception;

/**
 * Адаптер Gallery к ImageOwnerInterface.
 *
 * Реализует pessimistic lock через PessimisticLockBehavior Gallery,
 * чтобы исключить race condition при параллельной загрузке изображений
 * (несколько запросов одновременно видят main_image_id = null и пытаются
 * его установить, что приводит к FK constraint violation).
 */
readonly class GalleryImageOwner implements ImageOwnerInterface
{
    public function __construct(
        private Gallery           $gallery,
        private GalleryRepository $repository,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getOwnerId(): int
    {
        return $this->gallery->id;
    }

    /**
     * {@inheritdoc}
     *
     * @return Image[]
     */
    public function getOwnedImages(): array
    {
        return $this->gallery->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getMainImageId(): ?int
    {
        return $this->gallery->main_image_id ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMainImageId(?int $imageId): void
    {
        $this->gallery->setMainImage($imageId);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function saveOwner(): void
    {
        $this->repository->save($this->gallery);
    }

    /**
     * Блокирует строку галереи (SELECT FOR UPDATE) до конца транзакции.
     *
     * Исключает race condition при параллельной загрузке нескольких файлов.
     * @throws Exception
     */
    public function lockOwner(): void
    {
        $this->gallery->lock();
    }

    /**
     * Обновляет данные галереи из БД после применения блокировки.
     */
    public function refreshOwner(): void
    {
        $this->gallery->refresh();
    }
}
