<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\forms\backend\gallery;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Meta\MetaForm;
use Besnovatyj\Gallery\entities\gallery\Gallery;

/**
 * @property MetaForm $meta
 * @property CategoriesForm $categories
 * @property TagsForm $tags
 */
class GalleryForm extends CompositeForm
{
    public string $name = '';
    public string $description = '';
    public int|null $status = null;

    public function __construct(?Gallery $gallery = null, $config = [])
    {
        if ($gallery) {
            $this->name = $gallery->name;
            $this->description = $gallery->description;
            $this->status = $gallery->status;
            $this->meta = new MetaForm($gallery->meta);
            $this->categories = new CategoriesForm($gallery);
            $this->tags = new TagsForm($gallery);
        } else {
            $this->meta = new MetaForm();
            $this->categories = new CategoriesForm();
            $this->tags = new TagsForm();
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['name', 'status'], 'required'],
            [['name'], 'string', 'max' => 255],
            ['description', 'string'],
            ['status', 'integer'],
            ['status', 'in', 'range' => [Gallery::STATUS_DRAFT, Gallery::STATUS_ACTIVE]],
        ];
    }

    protected function internalForms(): array
    {
        return ['meta', 'categories', 'tags'];
    }

    public function statusList(): array
    {
        return [
            Gallery::STATUS_DRAFT => 'DRAFT',
            Gallery::STATUS_ACTIVE => 'ACTIVE',
        ];
    }
}
