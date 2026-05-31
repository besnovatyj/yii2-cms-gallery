<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\forms\backend\gallery;

use Besnovatyj\Helpers\StringHelper;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class TagsForm extends Model
{
    public array $newTagsNames = [];

    public function __construct(Gallery $gallery = null, $config = [])
    {
        if ($gallery) {
            $this->newTagsNames = ArrayHelper::map($gallery->tags, 'id', 'name');
        }
        parent::__construct($config);
    }

    public function beforeValidate(): bool
    {
        if (!empty($this->newTagsNames)) {
            $this->newTagsNames = array_filter(array_map(static function ($tagName) {
                return StringHelper::spaceReplace($tagName);
            }, array_values($this->newTagsNames)
            ));
        } else {
            $this->newTagsNames = [];
        }
        return parent::beforeValidate();
    }

    public function rules(): array
    {
        return [
            ['newTagsNames', 'each', 'rule' => ['string', 'length' => [0, 255]]],
        ];
    }

}
