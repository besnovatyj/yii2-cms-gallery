<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Blocks\render\BlockViewContext;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\entities\gallery\Image;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * Блок «Фотографии галереи» (тип `Gallery:photos` модуля блоков), шаблон пакета.
 *
 * Нейтральная сетка Bootstrap: миниатюры ссылками на полные снимки. Раскладку из блока пакет не
 * применяет — её рисует тема, перекрывая файл: `themes/{тема}/modules/Gallery/views/blockTypes/photos.php`.
 *
 * Галереи нет (не опубликована, скрыт раздел, удалена) или она пуста — блок не выводится вовсе.
 */

/** @var View $this */
/** @var array{heading:string, more:bool, moreLabel:string} $data */
/** @var BlockViewContext $block */
/** @var Gallery|null $gallery от {@see \Besnovatyj\Gallery\blocks\GalleryPhotosData} */
/** @var Image[] $images */

if ($gallery === null || $images === []) {
    return;
}
?>
<section class="block-gallery-photos">
    <?php if ($data['heading'] !== ''): ?>
        <h2><?= Html::encode($data['heading']) ?></h2>
    <?php endif; ?>
    <div class="row row-cols-2 row-cols-md-4 g-2">
        <?php foreach ($images as $image): ?>
            <div class="col">
                <a href="<?= Html::encode($image->getUploadUrl('file')) ?>">
                    <img src="<?= Html::encode($image->getThumbUrl('file', 'gallery_widget')) ?>" class="img-fluid"
                         alt="<?= Html::encode($gallery->name) ?>" loading="lazy">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if ($data['more'] && $data['moreLabel'] !== ''): ?>
        <p class="mt-3"><?= Html::a(Html::encode($data['moreLabel']), Url::to(['/Gallery/gallery/gallery', 'id' => $gallery->id])) ?></p>
    <?php endif; ?>
</section>
