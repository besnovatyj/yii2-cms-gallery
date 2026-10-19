<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Gallery\blocks;

use Besnovatyj\Contracts\block\BlockType;
use Besnovatyj\Gallery\readModels\GalleryReadRepository;

/**
 * Тип блока «Фотографии галереи» для модуля блоков (`Gallery:photos`).
 *
 * Блок хранит выбор: какая галерея, какой раскладкой, сколько снимков и нужна ли ссылка на всю
 * галерею (адрес страницы галереи вычисляется, вводить его не нужно). Снимки отдаёт поставщик
 * данных {@see GalleryPhotosData} — только из опубликованной галереи в видимом разделе.
 *
 * Список галерей для поля выбора ленивый (замыкание): его загружают только форма блока и проверка
 * при сохранении, вывод блока на сайте список не перечитывает.
 *
 * Шаблон по умолчанию — `src/views/blockTypes/photos.php`; тема перекрывает его overlay-ем
 * представлений модуля: `themes/{тема}/modules/Gallery/views/blockTypes/photos.php`.
 */
final class GalleryPhotosBlockType
{
    /** Локальный id типа; полный — `Gallery:photos`. */
    public const string ID = 'photos';

    /** Раскладки: значение => подпись. Пакет рисует только сетку, ленту — тема. */
    public const array LAYOUTS = [
        'grid' => 'Сетка, как на странице галереи',
        'slider' => 'Лента (если тема её поддерживает)',
    ];

    public function __construct(private readonly GalleryReadRepository $galleries)
    {
    }

    public function definition(): BlockType
    {
        return new BlockType(
            id: self::ID,
            label: 'Фотографии галереи',
            view: dirname(__DIR__) . '/views/blockTypes/photos.php',
            fields: [
                'heading' => ['kind' => 'text', 'label' => 'Заголовок раздела', 'maxLength' => 120,
                    'hint' => 'Пусто — без заголовка.'],
                // Пока галерей нет — вариант-заглушка с пустым значением, его отвергнет проверка
                // обязательного поля.
                'gallery' => ['kind' => 'select', 'label' => 'Галерея', 'required' => true,
                    'options' => fn (): array => $this->galleries->options() ?: ['' => '— галерей ещё нет —'],
                    'hint' => 'Неопубликованная галерея или галерея в скрытом разделе на сайт не выводится.'],
                'layout' => ['kind' => 'select', 'label' => 'Раскладка', 'default' => 'grid', 'options' => self::LAYOUTS],
                'limit' => ['kind' => 'select', 'label' => 'Сколько снимков', 'default' => '12', 'options' => [
                    '' => 'все', '6' => '6', '8' => '8', '10' => '10', '12' => '12', '16' => '16', '20' => '20',
                ]],
                'more' => ['kind' => 'checkbox', 'label' => 'Ссылка на всю галерею', 'default' => true],
                'moreLabel' => ['kind' => 'text', 'label' => 'Подпись ссылки', 'default' => 'Вся галерея', 'maxLength' => 40],
            ],
            hint: 'Снимки одной галереи сеткой или лентой, с лайтбоксом.',
            dataProvider: GalleryPhotosData::class,
        );
    }
}
