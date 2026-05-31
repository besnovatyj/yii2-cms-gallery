<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\repositories;

use Besnovatyj\Gallery\entities\gallery\Gallery;
use RuntimeException;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class GalleryRepository
{

    public function get(int $id): Gallery
    {
        if (!$gallery = Gallery::findOne($id)) {
            throw new NotFoundException('Gallery is not found.');
        }
        return $gallery;
    }

    public function existsByMainCategory($id): bool
    {
        return Gallery::find()->andWhere(['category_id' => $id])->exists();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function save(Gallery $gallery)
    {
        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            try {
                if ($gallery->save()) {
                    return true;
                }
                throw new RuntimeException('Failed to save gallery.');
            } catch (Exception $e) {
                if ($e->errorInfo[1] == 1213) { // Код ошибки дедлока
                    $retryCount++;
                    if ($retryCount >= $maxRetries) {
                        throw $e; // Превышено количество попыток
                    }
                    usleep(rand(100, 500) * 1000); // Задержка 100-500 мс
                    continue;
                }
                throw $e; // Другие ошибки
            }
        }
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove(Gallery $gallery): void
    {
        if (!$gallery->delete()) {
            throw new RuntimeException('Removing error.');
        }
    }
}
