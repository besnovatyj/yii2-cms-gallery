<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Gallery;

use Besnovatyj\Kernel\module\CmsModule;
use Besnovatyj\Contracts\module\DeclaresModule;
use Besnovatyj\Contracts\module\ProvidesAdminMenu;
use Besnovatyj\Contracts\module\ProvidesDirectories;
use Besnovatyj\Contracts\module\ProvidesMigrations;
use Besnovatyj\Contracts\menu\MenuTarget;
use Besnovatyj\Contracts\menu\MenuTargetProvider;
use Besnovatyj\Contracts\search\SearchSource;
use Besnovatyj\Contracts\search\SearchableProvider;
use Besnovatyj\Contracts\sitemap\ChangeFrequency;
use Besnovatyj\Contracts\sitemap\SitemapFreshness;
use Besnovatyj\Contracts\sitemap\SitemapProvider;
use Besnovatyj\Contracts\sitemap\SitemapSection;
use Besnovatyj\Contracts\sitemap\SitemapUrl;
use Besnovatyj\Contracts\tags\TaggableProvider;
use Besnovatyj\Contracts\tags\TagSource;
use Besnovatyj\Gallery\entities\gallery\Gallery;
use Besnovatyj\Gallery\entities\Category;
use Besnovatyj\Gallery\readModels\CategoryReadRepository;
use Besnovatyj\Gallery\readModels\GalleryReadRepository;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

class Module extends CmsModule implements
    DeclaresModule, ProvidesAdminMenu,
    ProvidesDirectories, ProvidesMigrations, MenuTargetProvider, SearchableProvider,
    SitemapProvider, SitemapFreshness, TaggableProvider
{
    public const bool EDITABLE = true;
    public const string VERSION = '1.0.0';
    public const string MODULE_ID = 'Gallery';
    public static function moduleId(): string { return self::MODULE_ID; }
    public static function moduleVersion(): string { return self::VERSION; }
    public static function isEditable(): bool { return self::EDITABLE; }
    public static function adminMenu(): array { return require __DIR__.'/config/adminMenu.php'; }
    public static function moduleConfig(): array { return require __DIR__.'/config/config.php'; }
    public static function migrationPath(): string { return __DIR__.'/migrations'; }
    public static function migrationNamespace(): ?string { return __NAMESPACE__.'\\migrations'; }
    public static function directories(): array { return ['@static/origin/Gallery','@static/cache/Gallery'];}

    /**
     * Цели для построения пунктов меню. Реализация {@see MenuTargetProvider};
     * вызывается только модулем меню, если он установлен.
     *
     * @return MenuTarget[]
     */
    public function menuTargets(): array
    {
        return [
            new MenuTarget('/Gallery/gallery/category', 'Категория галереи', 'slug'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string,string>
     */
    public function menuCandidates(string $route): array
    {
        return match (ltrim($route, '/')) {
            'Gallery/gallery/category' => $this->categorySlugMap(),
            default => [],
        };
    }

    /**
     * Карта `slug => подпись` (с отступом по глубине дерева) для категорий галереи.
     *
     * @return array<string,string>
     */
    private function categorySlugMap(): array
    {
        return (new TreeQueryScope(Category::class))->dropdownTree(keyAttribute: 'slug', indent: '— ');
    }

    /**
     * Контент модуля для сквозного поиска. Реализация {@see SearchableProvider}; вызывается
     * только модулем поиска, если он установлен.
     *
     * @return SearchSource[]
     */
    public function searchSources(): array
    {
        return [
            new SearchSource('gallery.gallery', 'Галереи', 1.0, 'bi bi-images'),
            new SearchSource('gallery.category', 'Категории галерей', 0.7, 'bi bi-folder'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function searchDocuments(string $type): iterable
    {
        return match ($type) {
            'gallery.gallery' => (new GalleryReadRepository())->searchDocuments(),
            'gallery.category' => (new CategoryReadRepository())->searchDocuments(),
            default => [],
        };
    }


    /**
     * Галереи — участники общего словаря тегов. Реализация {@see TaggableProvider}; вызывается модулем
     * тегов для страницы `/tag/<slug>` и облака. Ключ — тот же `gallery.gallery`, что у поиска и карты.
     *
     * @return TagSource[]
     */
    public function tagSources(): array
    {
        return [
            new TagSource(Gallery::tagType(), 'Галереи', 'bi bi-images'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function visibleTaggedIds(string $type, array $ids): array
    {
        return match ($type) {
            Gallery::tagType() => new GalleryReadRepository()->visibleIds($ids),
            default => [],
        };
    }

    /**
     * {@inheritdoc}
     */
    public function taggedItems(string $type, array $ids): iterable
    {
        return match ($type) {
            Gallery::tagType() => new GalleryReadRepository()->taggedItems($ids),
            default => [],
        };
    }

    /**
     * Разделы карты сайта. Реализация {@see SitemapProvider}; вызывается только модулем карты,
     * если он установлен.
     *
     * Разделов два, и это не дублирование: «Галерея» — навигационная ветка (список и его
     * категории), «Альбомы» — сами альбомы. Каждый режется в свой файл, включается и взвешивается
     * отдельно, а на человеческой карте даёт свой блок.
     *
     * @return SitemapSection[]
     */
    public function sitemapSections(): array
    {
        return [
            new SitemapSection(
                key: 'gallery.category',
                label: 'Галерея',
                changeFrequency: ChangeFrequency::Weekly,
                priority: 0.5,
                order: 70,
                icon: 'bi bi-folder',
            ),
            new SitemapSection(
                key: 'gallery.gallery',
                label: 'Альбомы',
                changeFrequency: ChangeFrequency::Monthly,
                priority: 0.5,
                order: 75,
                icon: 'bi bi-images',
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function sitemapUrls(string $section): iterable
    {
        return match ($section) {
            'gallery.gallery' => (new GalleryReadRepository())->sitemapUrls(),
            'gallery.category' => $this->categorySitemapUrls(),
            default => [],
        };
    }

    /**
     * {@inheritdoc}
     *
     * Отпечаток есть только у альбомов: в дереве категорий колонок времени нет
     * (см. {@see CategoryReadRepository::sitemapUrls()}).
     */
    public function sitemapRevision(string $section): ?string
    {
        return match ($section) {
            'gallery.gallery' => (new GalleryReadRepository())->sitemapRevision(),
            default => null,
        };
    }

    /**
     * Категории галереи, а перед ними — сам список.
     *
     * Список — корень ветки и для робота, и для читателя: на человеческой карте он открывает блок,
     * в XML это обычный адрес с высоким приоритетом. Отдельным разделом карты его заводить незачем —
     * раздел из одного адреса только засоряет и настройки, и индекс файлов.
     *
     * @return iterable<SitemapUrl>
     */
    private function categorySitemapUrls(): iterable
    {
        yield new SitemapUrl(
            route: '/Gallery/gallery/index',
            title: 'Галерея',
            changeFrequency: ChangeFrequency::Weekly,
            priority: 0.9,
        );

        yield from (new CategoryReadRepository())->sitemapUrls();
    }
}
