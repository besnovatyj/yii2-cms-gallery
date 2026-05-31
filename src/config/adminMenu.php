<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

return [
    // Galleries
    [
        'label' => 'Galleries',
        'iconClass' => 'bi bi-images me-1',
        'url' => ['/Gallery/backend/gallery/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'Gallery/backend/gallery');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Gallery',
                    'groupIcon' => 'bi bi-image',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],
    // Categories
    [
        'label' => 'Categories',
        'iconClass' => 'bi bi-diagram-3 me-1',
        'url' => ['/Gallery/backend/category/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'Gallery/backend/category');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Gallery',
                    'groupIcon' => 'bi bi-image',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],
    // Tags
    [
        'label' => 'Tags',
        'iconClass' => 'bi bi-tags me-1',
        'url' => ['/Gallery/backend/tag/index'],
        'active' => static function () {
            return str_contains(\Yii::$app->request->url, 'Gallery/backend/tag');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'left-sidebar',
                    'group' => 'Gallery',
                    'groupIcon' => 'bi bi-image',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],
];
