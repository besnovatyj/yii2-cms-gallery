<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $category ?Category */

// Только видимые подкатегории: скрытая ветка не должна предлагаться ссылкой на фронте.
// `$category` может быть null — корень тоже снимается с публикации, как любой другой раздел.
$children = $category === null
    ? []
    : new TreeQueryScope(Category::class)->childrenQuery($category)->visible()->all();

?>

<?php if (count($children) > 0) : ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php foreach ($children as $child): ?>
            <a href="<?= Html::encode(Url::to(['/Gallery/gallery/category', 'slug' => $child->slug])) ?>"
               class="btn btn-outline-secondary btn-sm">
                <?= Html::encode($child->name) ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
