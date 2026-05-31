<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Gallery\repositories\GalleryRepository;
use Besnovatyj\Meta\Meta;
use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\TreeManager\Manager\entities\Node;
use Besnovatyj\TreeManager\Manager\forms\TreeNodeFormInterface;
use Besnovatyj\TreeManager\Manager\TreeManager;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

/**
 * Конфигурация DI контейнера для модуля Gallery
 */
return function (\yii\di\Container $container): void {
    $container->setSingleton('gallery.tree.manager', function () use ($container) {
        $galleriesRepo = new GalleryRepository();
        return new TreeManager(
            modelClass: Category::class,
            entityFactory: function (TreeNodeFormInterface $form): Category {
                return Category::create(
                    $form->name,
                    $form->slug,
                    $form->description,
                    new Meta(
                        $form->meta->title,
                        $form->meta->description,
                        $form->meta->keywords,
                    ),
                );
            },
            entityUpdater: function (Node $node, TreeNodeFormInterface $form): Node {
                /** @var Category $node */
                $node->edit(
                    $form->name,
                    $form->slug,
                    $form->description,
                    new Meta(
                        $form->meta->title,
                        $form->meta->description,
                        $form->meta->keywords,
                    ),
                );
                return $node;
            },
            deleteGuard: function (Node $node) use ($galleriesRepo): void {
                /** @var Category $node */
                // TODO нет проверки на запрет удаления родительской, если к дочерней привязаны элементы
                if ($galleriesRepo->existsByMainCategory($node->id)) {
                    throw new DomainException('Unable to remove taxonomy with performances.');
                }
            },
        );
    });
    $container->setSingleton('gallery.tree.scope', function () use ($container) {
        return new TreeQueryScope(Category::class);
    });
};
