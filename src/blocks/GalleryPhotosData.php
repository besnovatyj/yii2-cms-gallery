<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Gallery\blocks;

use Besnovatyj\Contracts\block\BlockDataProvider;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\entities\gallery\Image;
use Besnovatyj\Gallery\readModels\GalleryReadRepository;

/**
 * Галерея и её снимки для блока «Фотографии галереи» ({@see GalleryPhotosBlockType}).
 *
 * Галерея читается через {@see GalleryReadRepository::find()} — только опубликованная и в видимом
 * разделе. Снимки — в порядке галереи, ограничение числа — запросом, а не обрезкой в PHP: в
 * галерее могут быть сотни снимков. Выборка не кэшируется, как и страница самой галереи.
 *
 * Создаётся DI-контейнером модулем блоков при выводе блока.
 */
final class GalleryPhotosData implements BlockDataProvider
{
    public function __construct(private readonly GalleryReadRepository $galleries)
    {
    }

    /**
     * @param array{gallery:string, limit:string} $data
     * @return array{gallery: Gallery|null, images: Image[]}
     */
    public function provide(array $data): array
    {
        $id = (string)($data['gallery'] ?? '');
        $gallery = ctype_digit($id) ? $this->galleries->find((int)$id) : null;
        if ($gallery === null) {
            return ['gallery' => null, 'images' => []];
        }

        $limit = (string)($data['limit'] ?? '');
        $query = $gallery->getImages();
        if (ctype_digit($limit) && (int)$limit > 0) {
            $query->limit(min((int)$limit, 100));
        }

        return ['gallery' => $gallery, 'images' => $query->all()];
    }
}
