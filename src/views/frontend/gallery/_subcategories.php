<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $category Category */

$children = new TreeQueryScope(Category::class)->childrenQuery($category)->all();

?>

<?php if (count($children) > 0) : ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php foreach ($children as $child): ?>
            <a href="<?= Html::encode(Url::to(['/Gallery/gallery/category', 'id' => $child->id])) ?>"
               class="btn btn-outline-secondary btn-sm">
                <?= Html::encode($child->name) ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
