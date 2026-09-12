<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Gallery\Module;

/**
 * Yii2-конфиг модуля для движка yiisoft/config (группа `common` — общий для всех приложений).
 *
 * Объявляется через `extra.config-plugin`, собирается modman в merge-plan и мёржится в рантайме.
 * Содержит регистрацию модуля. Меню (adminMenu) и миграции остаются вкладами modman. Значения берутся
 * из статических методов {@see Module} — единый источник, без дублирования.
 *
 * URL-правила фронтенда — вклад в `frontendUrlManager` группы `common` (см. README_Yii2_Modules.md), по
 * образцу documents. Первый сегмент роута капитализирован под реальный id модуля 'Gallery'. Гейтятся
 * modman. Старые адреса вида `/Gallery/gallery/gallery?id=5` продолжают работать: strict parsing выключен.
 *
 * Конвенция slug: начинается с буквы (`[a-z][\w\-]*`, см. {@see \Besnovatyj\Validators\SlugValidator}) —
 * иначе категория со slug из одних цифр перекрывалась бы правилом альбома `gallery/<id:\d+>`.
 */
return [
    'modules' => [
        Module::moduleId() => array_merge(
            ['class' => Module::class],
            Module::moduleConfig(),
            ['version' => Module::moduleVersion()],
        ),
    ],
    'components' => [
        'frontendUrlManager' => [
            'rules' => [
                'gallery'                                => 'Gallery/gallery/index',
                'gallery/tag/<id:\d+>'                   => 'Gallery/gallery/tag',
                'gallery/<id:\d+>'                       => 'Gallery/gallery/gallery',
                'gallery/<slug:[a-z][\w\-]*>/<page:\d+>' => 'Gallery/gallery/category', // <page> — пагинация
                'gallery/<slug:[a-z][\w\-]*>'            => 'Gallery/gallery/category',
            ],
        ],
    ],
];
