<?php
/** Повторно используемые блоки интерфейса (дизайн «Тёмная галерея»). */
declare(strict_types=1);

function icon(string $name, string $class = ''): string
{
    return '<svg class="ico ' . e($class) . '" aria-hidden="true" focusable="false"><use href="#i-' . e($name) . '"/></svg>';
}

function logo(): string
{
    return 'объектив<i>.</i>';
}

/** Кнопка «сердце» (ФТ-13). Гостю предлагается войти. */
function fav_button(int $photographerId, string $label = 'В избранное'): string
{
    $on = is_favorite($photographerId);
    return sprintf(
        '<button type="button" class="icon-btn fav-btn%s" data-fav="%d" aria-pressed="%s" aria-label="%s">%s</button>',
        $on ? ' is-on' : '', $photographerId, $on ? 'true' : 'false', e($label), icon('heart')
    );
}

/** Работы фотографа, сдвинутые по его id, чтобы у соседних карточек не совпадали первые снимки. */
function lead_works(array $p, int $n): array
{
    $k = $p['id'] % max(1, count($p['works']));
    $w = array_merge(array_slice($p['works'], $k), array_slice($p['works'], 0, $k));
    return array_slice($w, 0, $n);
}

/** Пропорция снимка для раскладки по рядам: ширина ряда делится по пропорциям фото, порядок — слева направо. */
function ratio_style(string $file): string
{
    [$w, $h] = photo_size($file);
    return ' style="--r:' . round($w / max(1, $h), 3) . '"';
}

/** Работа в ленте (главная, «Работы»): снимок, название, автор и жанр. */
function work_figure(array $w, bool $showTime = false, bool $isNew = false): string
{
    $meta = $w['author'] . ', ' . mb_strtolower(genre_name($w['genre'])) . ($showTime ? ', ' . time_ago($w['hours_ago']) : '');
    return '<figure' . ratio_style($w['file']) . '><a href="' . e(url('photographer.php', ['id' => $w['author_id']])) . '">'
        . img_tag($w['file'], $w['title'] . ' — ' . $w['author'])
        . '<figcaption><b>' . e($w['title']) . '</b>' . ($isNew ? '<span class="badge">Новое</span>' : '')
        . '<span class="mt">' . e($meta) . '</span></figcaption></a></figure>';
}

/** Строка фотографа на главной: имя и жанры слева, крупная лента работ справа. */
function photographer_row(array $p, int $i): string
{
    $catalog = photo_catalog();
    $th = '';
    foreach (lead_works($p, 4) as $key) {
        $th .= img_tag($catalog[$key]['file'], $catalog[$key]['title'] . ' — ' . $p['name']);
    }
    return '<a class="row" href="' . e(url('photographer.php', ['id' => $p['id']])) . '">'
        . '<span class="row__who"><span class="nm">' . e($p['name']) . '</span>'
        . '<span class="gn">' . e(genre_names($p['genres'])) . '</span>'
        . '<span class="pr">от ' . e(money($p['price'])) . ' в час</span></span>'
        . '<span class="th">' . $th . '</span></a>';
}

/** Карточка фотографа в каталоге (ФТ-02): коллаж из трёх работ, имя, жанры, цена и действия. */
function photographer_card(array $p): string
{
    $profile = url('photographer.php', ['id' => $p['id']]);
    $catalog = photo_catalog();
    $col = '';
    foreach (lead_works($p, 3) as $key) {
        $col .= img_tag($catalog[$key]['file'], $catalog[$key]['title']);
    }
    return '<article class="pc"><a class="col" href="' . e($profile) . '" tabindex="-1" aria-hidden="true">' . $col . '</a>'
        . '<h3 class="pc__name"><a href="' . e($profile) . '">' . e($p['name']) . '</a></h3>'
        . '<p class="pc__genres">' . e(genre_names($p['genres'])) . ', ' . e($p['city']) . '</p>'
        . '<div class="pc__meta"><span class="pr">от ' . e(money($p['price'])) . ' в час</span><span class="pc__actions">'
        . '<button type="button" class="btn btn--sm" data-open-request data-id="' . $p['id'] . '" data-name="' . e($p['name']) . '" data-dative="' . e(dative_first_name($p['name'])) . '" data-avatar="' . e(photo_url($p['avatar'])) . '">Написать</button>'
        . fav_button($p['id'], 'Добавить ' . $p['name'] . ' в избранное') . '</span></div></article>';
}

/** Пагинация (ФТ-04). */
function pagination(int $page, int $pages, string $base, array $params): string
{
    if ($pages <= 1) {
        return '';
    }
    $html = '<nav class="pg" aria-label="Страницы">';
    $show = [];
    for ($i = 1; $i <= $pages; $i++) {
        if ($i <= 3 || $i > $pages - 1 || abs($i - $page) <= 1) {
            $show[] = $i;
        }
    }
    $prev = 0;
    foreach ($show as $i) {
        if ($prev && $i - $prev > 1) {
            $html .= '<span aria-hidden="true">…</span>';
        }
        $cur = $i === $page;
        $html .= sprintf(
            '<a%s href="%s"%s aria-label="Страница %d">%d</a>',
            $cur ? ' class="is-current"' : '', e(url($base, array_merge($params, ['page' => $i > 1 ? $i : null]))),
            $cur ? ' aria-current="page"' : '', $i, $i
        );
        $prev = $i;
    }
    if ($page < $pages) {
        $html .= '<a class="pg__next" href="' . e(url($base, array_merge($params, ['page' => $page + 1]))) . '">Дальше</a>';
    }
    return $html . '</nav>';
}

/** Пояснение об ориентировочных ценах (ТЗ 4.2, критерии приёмки). */
function price_note(): string
{
    return '<p class="note">Цены указаны фотографами как ориентировочные («от …») и не являются офертой.</p>';
}

/** Призыв к действию внизу страниц. */
function cta_block(string $title = 'Снимаете? Покажите свои работы заказчикам.', string $text = 'Профиль бесплатный, заводится за пару минут. Заявки приходят в личный кабинет.'): string
{
    return '<section class="cta"><div><h2>' . $title . '</h2><p>' . e($text) . '</p></div>'
        . '<button type="button" class="btn btn--primary btn--lg" data-open-modal="register">Создать профиль</button></section>';
}

/** Поле формы: подпись, элемент ввода и место под сообщение об ошибке. */
function field(string $name, string $label, string $control, string $extra = ''): string
{
    return '<div class="field"><small>' . e($label) . $extra . '</small>' . $control . '<p class="err" data-error-for="' . e($name) . '" role="alert"></p></div>';
}
