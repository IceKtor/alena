<?php
declare(strict_types=1);

/** Экранирование при выводе (ТЗ 11.3). */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Ссылка на страницу с параметрами. */
function url(string $page, array $params = []): string
{
    $params = array_filter($params, fn($v) => $v !== null && $v !== '' && $v !== []);
    return $page . ($params ? '?' . http_build_query($params) : '');
}

/** Текущие GET-параметры с заменой части из $over (null — удалить). */
function qs(array $over = []): array
{
    return array_merge($_GET, $over);
}

function photo_url(string $file): string
{
    return 'img/photos/' . $file;
}

/** Размеры картинки для width/height (чтобы не было сдвигов, ТЗ 16.1). */
function photo_size(string $file): array
{
    static $cache = [];
    if (!isset($cache[$file])) {
        $s = @getimagesize(__DIR__ . '/../img/photos/' . $file);
        $cache[$file] = $s ? [$s[0], $s[1]] : [4, 3];
    }
    return $cache[$file];
}

function img_tag(string $file, string $alt, string $class = '', bool $lazy = true): string
{
    [$w, $h] = photo_size($file);
    return sprintf(
        '<img src="%s" alt="%s" width="%d" height="%d"%s%s>',
        e(photo_url($file)), e($alt), $w, $h,
        $class !== '' ? ' class="' . e($class) . '"' : '',
        $lazy ? ' loading="lazy" decoding="async"' : ''
    );
}

function genre_name(string $slug): string
{
    return genres()[$slug] ?? $slug;
}

function genre_names(array $slugs): string
{
    return implode(', ', array_map('genre_name', $slugs));
}

/** «от 3 000 ₽/час» */
function money(int $n): string
{
    return number_format($n, 0, ',', "\u{00A0}") . "\u{00A0}₽";
}

function plural(int $n, string $one, string $few, string $many): string
{
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) {
        return $many;
    }
    if ($n1 > 1 && $n1 < 5) {
        return $few;
    }
    return $n1 === 1 ? $one : $many;
}

function time_ago(int $hours): string
{
    if ($hours < 1) {
        return 'только что';
    }
    if ($hours < 24) {
        return $hours . ' ч назад';
    }
    $d = intdiv($hours, 24);
    return $d . ' ' . plural($d, 'день', 'дня', 'дней') . ' назад';
}

/**
 * Поиск, фильтры, сортировка по правилам ТЗ 8.3:
 * фильтры объединяются по «И», внутри «Жанр» — по «ИЛИ»;
 * включённый переключатель оставляет только фотографов с этой возможностью;
 * при равенстве сортировки порядок задаёт id (детерминированный результат).
 * TODO(DB): заменить на один SQL-запрос с WHERE … ORDER BY … LIMIT/OFFSET.
 */
function search_photographers(array $f): array
{
    $res = array_values(array_filter(photographers(), function ($p) use ($f) {
        if ($f['city'] !== '' && $p['city'] !== $f['city']) {
            return false;
        }
        if ($f['genres'] && !array_intersect($f['genres'], $p['genres'])) {
            return false;
        }
        if ($p['price'] < $f['price_min'] || $p['price'] > $f['price_max']) {
            return false;
        }
        foreach (['night', 'video', 'trip'] as $flag) {
            if ($f[$flag] && !$p[$flag]) {
                return false;
            }
        }
        return true;
    }));
    usort($res, function ($a, $b) use ($f) {
        return match ($f['sort']) {
            'price_asc'  => [$a['price'], $a['id']] <=> [$b['price'], $b['id']],
            'price_desc' => [$b['price'], $a['id']] <=> [$a['price'], $b['id']],
            default      => [$a['created'], $a['id']] <=> [$b['created'], $b['id']],
        };
    });
    return $res;
}

/** Чтение и проверка фильтров из GET (значения по умолчанию — ТЗ 8.3). */
function read_filters(array $src): array
{
    $genresAll = genres();
    $g = array_values(array_filter((array) ($src['genre'] ?? []), fn($x) => is_string($x) && isset($genresAll[$x])));
    $min = (int) ($src['price_min'] ?? PRICE_MIN);
    $max = (int) ($src['price_max'] ?? PRICE_MAX);
    $min = max(PRICE_MIN, min(PRICE_MAX, $min));
    $max = max(PRICE_MIN, min(PRICE_MAX, $max));
    $priceError = $min > $max;               // «Нижняя цена не может быть больше верхней»
    if ($priceError) {
        [$min, $max] = [$max, $min];
    }
    $city = (string) ($src['city'] ?? DEFAULT_CITY);
    if ($city !== '' && !in_array($city, cities(), true)) {
        $city = DEFAULT_CITY;
    }
    $sort = (string) ($src['sort'] ?? 'new');
    if (!in_array($sort, ['new', 'price_asc', 'price_desc'], true)) {
        $sort = 'new';
    }
    return [
        'city' => $city, 'genres' => $g, 'price_min' => $min, 'price_max' => $max,
        'night' => !empty($src['night']), 'video' => !empty($src['video']), 'trip' => !empty($src['trip']),
        'sort' => $sort, 'price_error' => $priceError,
    ];
}

function json_out(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Страница ошибки 404 (ТЗ 16.1: корректные коды ответа). */
function not_found(): never
{
    http_response_code(404);
    require __DIR__ . '/../404.php';
    exit;
}
