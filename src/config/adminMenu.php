<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Contracts\adminMenu\AdminMenuLocation;
use Besnovatyj\Contracts\adminMenu\AdminMenuPlacement;

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
                new AdminMenuPlacement(
                    location: AdminMenuLocation::LeftSidebar,
                    group: 'Gallery',
                    groupIcon: 'bi bi-image',
                    groupPriority: 100,
                    priority: 100,
                ),
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
                new AdminMenuPlacement(
                    location: AdminMenuLocation::LeftSidebar,
                    group: 'Gallery',
                    groupIcon: 'bi bi-image',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],
];
