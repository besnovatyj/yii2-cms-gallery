<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Gallery\entities\gallery\Gallery;

/** @var $gallery \Besnovatyj\Gallery\entities\gallery\Gallery */
/** @var $isThumb bool */

?>

<?php if (!$isThumb): ?>
    <ul class="thumbnails">
        <?php foreach ($gallery->images as $i => $image): ?>
            <?php if ($i !== 0): ?>
                <li class="image-additional">
                    <a class="thumbnail" href="<?= $image->getUploadUrl('file') ?>">
                        <img src="<?= $image->getThumbUrl('file', 'gallery_image_thumb') ?>"
                             alt=""/>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <ul class="thumbnails">
        <?php foreach ($gallery->images as $i => $image): ?>
            <?php if ($i == 0): ?>
                <li>
                    <a class="thumbnail" href="<?= $image->getUploadUrl('file') ?>">
                        <img src="<?= $image->getThumbUrl('file', 'catalog_list') ?>" alt=""/>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>
