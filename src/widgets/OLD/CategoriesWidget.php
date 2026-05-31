<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery\widgets\OLD;

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\readModels\CategoryReadRepository;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Виджет со списком категорий для сайдбара фронтэнда
 */
class CategoriesWidget extends Widget
{
    /** @var Category|null */
    public $active;
    private $categories;

    public function __construct(CategoryReadRepository $categories, $config = [])
    {
        parent::__construct($config);
        $this->categories = $categories;
    }

    public function run(): string
    {
        return Html::tag('ul', implode(PHP_EOL, array_map(function (Category $category) {
            $indent = ($category->depth > 1 ? str_repeat('&nbsp;&nbsp;&nbsp;', $category->depth - 1) . '- ' : '');
            $active = $this->active && ($this->active->id == $category->id || $this->active->isChildOf($category));
            return '<li  class="bo5-b p-t-8 p-b-8">' . Html::a(
                    $indent . Html::encode($category->name),
                    ['/Gallery/gallery/category', 'id' => $category->id],
                    ['class' => $active ? 'txt27 active' : 'txt27']
                ) . '</li>';
        }, $this->categories->getTreeWithSubsOf($this->active))), [
            'class' => 'list-group',
        ]);
    }

    // TODO закоментил при включении темы PATO, вынести переделанный виджет в тему и вернуть как было
//    public function run(): string
//    {
//        return Html::tag('div', implode(PHP_EOL, array_map(function (Category $category) {
//            $indent = ($category->depth > 1 ? str_repeat('&nbsp;&nbsp;&nbsp;', $category->depth - 1) . '- ' : '');
//            $active = $this->active && ($this->active->id == $category->id || $this->active->isChildOf($category));
//            return Html::a(
//                $indent . Html::encode($category->name),
//                ['/Gallery/gallery/category', 'id' => $category->id],
//                ['class' => $active ? 'list-group-item active' : 'list-group-item']
//            );
//        }, $this->categories->getTreeWithSubsOf($this->active))), [
//            'class' => 'list-group',
//        ]);
//    }


}
