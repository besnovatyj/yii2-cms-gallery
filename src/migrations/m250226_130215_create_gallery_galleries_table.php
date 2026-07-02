<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<n>' */
class m250226_130215_create_gallery_galleries_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%gallery_galleries}}';

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
            'category_id' => $this->integer()->notNull()
                ->comment('Идентификатор категории галереи'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('NOW()')
                ->comment('Дата создания галереи'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('NOW()')->append('ON UPDATE NOW()')
                ->comment('Дата последнего редактирования галереи'),
            'name' => $this->string(255)->notNull()
                ->comment('Название галереи'),
            'description' => $this->text()->null()
                ->comment('Описание галереи'),
            'main_image_id' => $this->integer()->null()
                ->comment('Идентификатор основной фотографии галереи'),
            'status' => $this->smallInteger(1)->notNull()->defaultValue(0)
                ->comment('Статус отображения галереи'),
            'meta_json' => $this->text()->notNull()
                ->comment('JSON meta'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Галереи');

        $this->createIndexes(static::TABLE_NAME, 'category_id');
        $this->createIndexes(static::TABLE_NAME, 'main_image_id');

        parent::safeUp();
    }

}
