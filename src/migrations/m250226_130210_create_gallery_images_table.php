<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\migrations;

use common\components\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<n>' */
class m250226_130210_create_gallery_images_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%gallery_images}}';

    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'gallery_id' => $this->integer()->notNull()
                ->comment('ID галереи.'),
            'file' => $this->string(255)->notNull()
                ->comment('Файл фотографии'),
            'sort' => $this->integer()->notNull()
                ->comment('Сортировка фотографии в конкретной галерее'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Изображения для галереи');

        $this->createIndexes(static::TABLE_NAME, 'gallery_id');

        parent::safeUp();
    }

}
