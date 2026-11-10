<?php
/**
 * Слой данных платформы.
 *
 * ┌──────────────────────────────────────────────────────────────────────┐
 * │ ЗАГЛУШКА. Сейчас все функции отдают тестовые данные вымышленных лиц  │
 * │ (ТЗ, допущение А-15). Когда будет база данных (MySQL + phpMyAdmin),  │
 * │ заменить тело каждой функции запросом через PDO (см. server/db.php). │
 * │ Сигнатуры и формат возвращаемых массивов менять не нужно —           │
 * │ страницы уже работают с ними.                                        │
 * └──────────────────────────────────────────────────────────────────────┘
 */
declare(strict_types=1);

/** Справочник «Жанр» (ТЗ, приложение Б). TODO(DB): таблица genres. */
function genres(): array
{
    return [
        'portrait'  => 'Портрет',
        'landscape' => 'Пейзаж',
        'reportage' => 'Репортаж',
        'still'     => 'Предметная',
        'wedding'   => 'Свадебная',
        'family'    => 'Семейная',
        'lovestory' => 'Love Story',
    ];
}

/** Города. TODO(DB): таблица cities. */
function cities(): array
{
    return ['Якутск', 'Мирный', 'Нерюнгри', 'Покровск'];
}

/** Файлы-заглушки вместо фото (из дизайн-системы). TODO(DB): таблица works + uploads/. */
function photo_catalog(): array
{
    return [
        'pier'       => ['file' => 'pier.jpg',       'title' => 'Причал на закате',  'genre' => 'landscape'],
        'camera'     => ['file' => 'camera.jpg',     'title' => 'Девушка с камерой', 'genre' => 'portrait'],
        'lighthouse' => ['file' => 'lighthouse.jpg', 'title' => 'Маяк в сумерках',   'genre' => 'landscape'],
        'city'       => ['file' => 'city.jpg',       'title' => 'Город днём',        'genre' => 'reportage'],
        'tree'       => ['file' => 'tree.jpg',       'title' => 'Дерево на закате',  'genre' => 'landscape'],
        'hat'        => ['file' => 'hat.jpg',        'title' => 'Взгляд из зелени',  'genre' => 'portrait'],
        'bike'       => ['file' => 'bike.jpg',       'title' => 'Старый велосипед',  'genre' => 'still'],
        'road'       => ['file' => 'road.jpg',       'title' => 'Ночная трасса',     'genre' => 'reportage'],
        'dancer'     => ['file' => 'dancer.jpg',     'title' => 'Прыжок',            'genre' => 'reportage'],
        'reflection' => ['file' => 'reflection.jpg', 'title' => 'Огни у воды',       'genre' => 'landscape'],
        'root'       => ['file' => 'root.jpg',       'title' => 'Корни леса',        'genre' => 'landscape'],
        'drop'       => ['file' => 'drop.jpg',       'title' => 'Капля',             'genre' => 'still'],
        'statue'     => ['file' => 'statue.jpg',     'title' => 'Свобода',           'genre' => 'reportage'],
    ];
}

/**
 * Фотографы (ФТ-02, ФТ-05). TODO(DB): users + photographer_profiles +
 * связь «многие ко многим» с genres (ТЗ, раздел 8.2).
 * Порядок строк: [id, имя, город, жанры, цена «от», стаж, ночная, видео, выезд, дней назад, аватар]
 */
function photographers(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $rows = [
        [1,  'Алексей Петров',     'Якутск',   ['portrait', 'landscape', 'reportage'], 3000, 8,  1, 0, 1, 2,  1, ['hat', 'pier', 'tree', 'bike', 'city', 'road', 'dancer', 'reflection', 'root', 'drop', 'lighthouse', 'camera']],
        [2,  'Мария Саввина',      'Якутск',   ['portrait', 'reportage'],              2500, 5,  0, 1, 0, 5,  2, ['camera', 'dancer', 'reflection', 'hat', 'city', 'road', 'statue', 'bike', 'drop']],
        [3,  'Айсен Охлопков',     'Якутск',   ['landscape', 'reportage'],             3500, 7,  1, 1, 1, 9,  3, ['lighthouse', 'pier', 'tree', 'reflection', 'root', 'city', 'road', 'statue', 'drop', 'hat']],
        [4,  'Дмитрий Колесов',    'Якутск',   ['portrait', 'still'],                  4000, 10, 0, 0, 0, 12, 4, ['dancer', 'bike', 'drop', 'hat', 'camera', 'statue', 'root', 'pier', 'tree']],
        [5,  'Николай Егоров',     'Якутск',   ['reportage', 'still'],                 2800, 4,  1, 0, 1, 15, 5, ['city', 'road', 'dancer', 'statue', 'bike', 'drop', 'reflection', 'tree', 'pier']],
        [6,  'Сергей Ким',         'Якутск',   ['landscape', 'still'],                 3200, 6,  0, 0, 1, 20, 6, ['root', 'statue', 'pier', 'lighthouse', 'tree', 'drop', 'bike', 'reflection', 'city']],
        [7,  'Саргылана Иванова',  'Якутск',   ['wedding', 'lovestory', 'portrait'],   5500, 9,  0, 1, 1, 1,  2, ['camera', 'hat', 'pier', 'tree', 'reflection', 'dancer', 'drop', 'lighthouse', 'root']],
        [8,  'Туйаара Попова',     'Якутск',   ['family', 'portrait'],                 3800, 6,  0, 0, 1, 3,  2, ['hat', 'camera', 'root', 'pier', 'drop', 'tree', 'dancer', 'bike', 'city']],
        [9,  'Евгений Макаров',    'Якутск',   ['wedding', 'reportage'],               7000, 12, 1, 1, 1, 4,  3, ['dancer', 'city', 'road', 'statue', 'camera', 'hat', 'reflection', 'pier', 'tree']],
        [10, 'Анна Григорьева',    'Мирный',   ['family', 'lovestory'],                3000, 3,  0, 0, 1, 7,  5, ['camera', 'pier', 'root', 'tree', 'drop', 'hat', 'reflection', 'bike', 'lighthouse']],
        [11, 'Виктор Громов',      'Нерюнгри', ['reportage', 'landscape'],             2600, 5,  1, 1, 1, 8,  4, ['road', 'city', 'lighthouse', 'tree', 'statue', 'reflection', 'pier', 'root', 'dancer']],
        [12, 'Лена Борисова',      'Якутск',   ['still', 'portrait'],                  1800, 2,  0, 0, 0, 10, 2, ['drop', 'bike', 'camera', 'hat', 'root', 'statue', 'pier', 'tree', 'dancer']],
        [13, 'Константин Яковлев', 'Якутск',   ['wedding', 'family'],                  9500, 15, 0, 1, 1, 11, 6, ['hat', 'camera', 'pier', 'dancer', 'tree', 'drop', 'lighthouse', 'reflection', 'city']],
        [14, 'Ольга Сидорова',     'Покровск', ['portrait', 'lovestory'],              2200, 4,  0, 0, 1, 13, 5, ['camera', 'hat', 'tree', 'pier', 'reflection', 'drop', 'root', 'bike', 'dancer']],
        [15, 'Павел Никитин',      'Якутск',   ['landscape', 'reportage', 'still'],    2900, 7,  1, 0, 1, 16, 3, ['lighthouse', 'tree', 'road', 'city', 'bike', 'statue', 'pier', 'root', 'reflection']],
        [16, 'Мичил Алексеев',     'Якутск',   ['reportage'],                          12000, 20, 1, 1, 1, 18, 1, ['road', 'city', 'dancer', 'statue', 'reflection', 'lighthouse', 'bike', 'hat', 'tree']],
        [17, 'Дарья Андреева',     'Мирный',   ['portrait', 'family'],                 3300, 5,  0, 0, 0, 22, 2, ['hat', 'camera', 'drop', 'root', 'pier', 'tree', 'dancer', 'reflection', 'bike']],
        [18, 'Артём Васильев',     'Якутск',   ['still', 'landscape'],                 600,  1,  0, 0, 0, 25, 4, ['bike', 'drop', 'root', 'pier', 'lighthouse', 'tree', 'statue', 'reflection', 'city']],
    ];
    $about = [
        'Снимаю портреты, города и пейзажи на плёнку и цифру. Люблю естественный свет, живые эмоции и честные кадры без постановки.',
        'Работаю в Якутии с 2018 года. Предпочитаю репортажный стиль: меньше позирования, больше настроения момента.',
        'Фотографирую людей и места, которые для них важны. Стараюсь, чтобы на съёмке было спокойно и немного весело.',
    ];
    $cache = [];
    foreach ($rows as $i => $r) {
        $cache[] = [
            'id'       => $r[0],
            'name'     => $r[1],
            'city'     => $r[2],
            'genres'   => $r[3],
            'price'    => $r[4],
            'exp'      => $r[5],
            'night'    => (bool) $r[6],
            'video'    => (bool) $r[7],
            'trip'     => (bool) $r[8],
            'created'  => $r[9],            // дней назад (для сортировки «Сначала новые»)
            'avatar'   => 'avatar' . $r[10] . '.jpg',
            'works'    => $r[11],           // ключи из photo_catalog()
            'about'    => $about[$i % count($about)],
            'dates'    => [15, 16, 22, 23, 29],
            'dates_note' => 'Ноябрь, ответ в течение дня',
        ];
    }
    return $cache;
}

function photographer_by_id(int $id): ?array
{
    foreach (photographers() as $p) {
        if ($p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

/** Услуги и цены фотографа (ФТ-05). TODO(DB): таблица services. */
function photographer_services(array $p): array
{
    $names = [
        'portrait' => 'Портретная съёмка', 'landscape' => 'Пейзаж и путешествия',
        'reportage' => 'Репортаж', 'still' => 'Предметная съёмка',
        'wedding' => 'Свадебная съёмка', 'family' => 'Семейная съёмка', 'lovestory' => 'Love Story',
    ];
    $out = [];
    foreach ($p['genres'] as $k => $g) {
        $out[] = ['title' => $names[$g], 'price' => $p['price'] + $k * 500];
    }
    return $out;
}

/** Работы фотографа (портфолио). TODO(DB): таблица works. */
function photographer_works(array $p): array
{
    $catalog = photo_catalog();
    $out = [];
    foreach ($p['works'] as $i => $key) {
        $c = $catalog[$key];
        $out[] = [
            'id'        => $p['id'] * 100 + $i,
            'key'       => $key,
            'file'      => $c['file'],
            'title'     => $c['title'],
            'genre'     => $c['genre'],
            'author_id' => $p['id'],
            'author'    => $p['name'],
            'hours_ago' => ($p['id'] * 7 + $i * 13) % 160 + 1 + ($p['created'] > 10 ? 24 : 0),
            'views'     => 120 + (($p['id'] * 31 + $i * 57) % 260),
        ];
    }
    return $out;
}

/** Общая лента работ, самые новые первыми (ФТ-06). TODO(DB): SELECT … FROM works ORDER BY created_at DESC. */
function all_works(?string $genre = null, string $sort = 'new'): array
{
    $works = [];
    foreach (photographers() as $p) {
        foreach (photographer_works($p) as $w) {
            if ($genre === null || $w['genre'] === $genre) {
                $works[] = $w;
            }
        }
    }
    usort($works, fn($a, $b) => [$a['hours_ago'], $a['id']] <=> [$b['hours_ago'], $b['id']]);
    return $works;
}

/** Заявки фотографа (ФТ-12). TODO(DB): таблица requests. */
function photographer_requests(int $photographerId): array
{
    return [
        ['id' => 3, 'name' => 'Мария',   'contact' => '+7 900 000-00-00', 'date' => '24 октября', 'genre' => 'portrait',  'message' => 'Хочу портретную съёмку в студии, около часа.',       'status' => 'new',      'created' => 'сегодня, 10:24'],
        ['id' => 2, 'name' => 'Сардаана', 'contact' => 'sardaana@mail.ru', 'date' => '2 ноября',   'genre' => 'reportage', 'message' => 'Нужен репортаж с корпоратива, 3 часа.',              'status' => 'viewed',   'created' => 'вчера, 18:02'],
        ['id' => 1, 'name' => 'Иван',    'contact' => '+7 914 111-22-33', 'date' => '10 ноября',   'genre' => 'landscape', 'message' => 'Съёмка пейзажей за городом, нужен выезд.',            'status' => 'done',     'created' => '3 дня назад'],
    ];
}

/** Статистика профиля (ФТ-09). TODO(DB): profile_views, favorites, requests. */
function photographer_stats(int $photographerId): array
{
    return ['views' => 1284, 'requests' => 12, 'favorites' => 87];
}

/** Статистика для «О платформе». TODO(DB): COUNT(*) по таблицам. */
function platform_stats(): array
{
    return [
        'photographers' => count(photographers()),
        'works'         => count(all_works()),
        'genres'        => count(genres()),
        'price'         => 0,
    ];
}

/* ───────────────────────── Записи (заглушки) ───────────────────────── */

/**
 * Сохранить заявку (ФТ-07). TODO(DB): INSERT INTO requests …
 * Вызывается ПОСЛЕ серверной проверки; транзакция — см. ТЗ 11.2.
 */
function save_request(array $data): bool
{
    // TODO(DB): сохранить и (желательно, ФТ-17) отправить письмо фотографу.
    return true;
}

/** Сообщение формы «Написать нам» (ФТ-06). TODO(DB): таблица feedback_messages. */
function save_feedback(array $data): bool
{
    return true;
}

/** Новая работа (ФТ-10). TODO(DB): INSERT INTO works; файл сохранить в uploads/, миниатюру создать. */
function save_work(array $data, array $file): bool
{
    return true;
}

/** Создать аккаунт (ФТ-08). TODO(DB): INSERT INTO users, password_hash() уже применён в auth.php. */
function save_user(array $data): array
{
    return ['id' => random_int(1000, 9999)] + $data;
}

/** Есть ли пользователь с такой почтой (уникальность, раздел 8.4). TODO(DB): SELECT 1 FROM users WHERE email = ?. */
function user_email_exists(string $email): bool
{
    return in_array(mb_strtolower($email), array_map(fn($u) => $u['email'], demo_users()), true);
}
