<?php
/** Повторно используемые блоки интерфейса. */
declare(strict_types=1);

function icon(string $name, string $class = ''): string
{
    return '<svg class="icon ' . e($class) . '" aria-hidden="true" focusable="false"><use href="#i-' . e($name) . '"/></svg>';
}

function logo(): string
{
    return '<span class="logo__mark" aria-hidden="true"><span class="logo__lens"></span></span><span class="logo__text">' . e(APP_NAME) . '</span>';
}

function stripe(): string
{
    return '<div class="stripe" aria-hidden="true"></div>';
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

/** Карточка работы (главная, лента «Работы»). */
function work_card(array $w, bool $showTime = false, bool $isNew = false, bool $withFav = false): string
{
    $meta = genre_name($w['genre']) . ', ' . $w['author'];
    $html  = '<article class="work">';
    $html .= '<a class="work__link" href="' . e(url('photographer.php', ['id' => $w['author_id']])) . '">';
    $html .= '<span class="work__img frame">' . img_tag($w['file'], $w['title'] . ' — ' . $w['author']);
    if ($isNew) {
        $html .= '<span class="badge badge--amber work__badge">Новое</span>';
    }
    $html .= '</span>';
    $html .= '<span class="work__row"><span class="work__title">' . e($w['title']) . '</span><span class="work__no mono">' . e(frame_no((int) $w['id'])) . '</span></span>';
    $html .= '<span class="work__meta">' . e($meta) . ($showTime ? ' · ' . e(time_ago($w['hours_ago'])) : '') . '</span>';
    $html .= '</a>';
    if ($withFav) {
        $html .= '<span class="work__fav">' . fav_button($w['author_id'], 'Добавить автора в избранное') . '</span>';
    }
    return $html . '</article>';
}

/** Карточка фотографа в каталоге (ФТ-02). */
function photographer_card(array $p): string
{
    $profile = url('photographer.php', ['id' => $p['id']]);
    $catalog = photo_catalog();
    $html  = '<article class="pcard card">';
    $html .= '<a class="pcard__thumbs" href="' . e($profile) . '" tabindex="-1" aria-hidden="true">';
    foreach (array_slice($p['works'], 0, 3) as $key) {
        $html .= img_tag($catalog[$key]['file'], $catalog[$key]['title']);
    }
    $html .= '</a>';
    $html .= '<div class="pcard__body">';
    $html .= '<img class="avatar pcard__avatar" src="' . e(photo_url($p['avatar'])) . '" alt="" width="72" height="72" loading="lazy">';
    $html .= '<div class="pcard__head"><div>';
    $html .= '<h3 class="pcard__name"><a href="' . e($profile) . '">' . e($p['name']) . '</a></h3>';
    $html .= '<p class="muted pcard__genres">' . e(genre_names($p['genres'])) . '</p></div>';
    $html .= '<div class="pcard__actions">';
    $html .= '<button type="button" class="icon-btn" data-open-request data-id="' . $p['id'] . '" data-name="' . e($p['name']) . '" data-avatar="' . e(photo_url($p['avatar'])) . '" aria-label="Написать фотографу ' . e($p['name']) . '">' . icon('mail') . '</button>';
    $html .= fav_button($p['id'], 'Добавить ' . $p['name'] . ' в избранное');
    $html .= '</div></div>';
    $html .= '<div class="pcard__foot"><span class="pcard__city">' . icon('pin') . e($p['city']) . '</span>';
    $html .= '<span class="mono">от ' . e(money($p['price'])) . '/час</span></div>';
    $html .= '</div></article>';
    return $html;
}

/** Пагинация: круглые кнопки (ФТ-04). */
function pagination(int $page, int $pages, string $base, array $params): string
{
    if ($pages <= 1) {
        return '';
    }
    $html = '<nav class="pagination" aria-label="Страницы">';
    $show = [];
    for ($i = 1; $i <= $pages; $i++) {
        if ($i <= 3 || $i > $pages - 1 || abs($i - $page) <= 1) {
            $show[] = $i;
        }
    }
    $prev = 0;
    foreach ($show as $i) {
        if ($prev && $i - $prev > 1) {
            $html .= '<span class="pagination__gap" aria-hidden="true">…</span>';
        }
        $cur = $i === $page;
        $html .= sprintf(
            '<a class="page-btn%s" href="%s"%s aria-label="Страница %d">%d</a>',
            $cur ? ' is-current' : '', e(url($base, array_merge($params, ['page' => $i > 1 ? $i : null]))),
            $cur ? ' aria-current="page"' : '', $i, $i
        );
        $prev = $i;
    }
    return $html . '</nav>';
}

/** Пояснение об ориентировочных ценах (ТЗ 4.2, критерии приёмки). */
function price_note(): string
{
    return '<p class="note">Цены указаны фотографами как ориентировочные («от …») и не являются офертой.</p>';
}
