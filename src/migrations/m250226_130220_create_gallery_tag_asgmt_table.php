<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<n>' */
class m250226_130220_create_gallery_tag_asgmt_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%gallery_tag_asgmt}}';

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
            'gallery_id' => $this->integer()->notNull()
                ->comment('Идентификатор'),
            'tag_id' => $this->integer()->notNull()
                ->comment('Идентификатор тега'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Связь галереи с тегами');

        $this->createIndexes(static::TABLE_NAME, ['gallery_id', 'tag_id'], true);
        $this->createIndexes(static::TABLE_NAME, 'gallery_id');
        $this->createIndexes(static::TABLE_NAME, 'tag_id');

        parent::safeUp();
    }

}
