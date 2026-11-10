<?php
/** Каталог фотографов (ФТ-02, ФТ-03, ФТ-04). Правила поиска — ТЗ 8.3. */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

$f      = read_filters($_GET);
$found  = search_photographers($f);
$total  = count($found);

// Живой счётчик для кнопки «Показать N» (js/app.js)
if (($_GET['format'] ?? '') === 'count') {
    json_out(['count' => $total, 'label' => 'Показать ' . $total]);
}

// Запоминаем адрес для кнопки «Назад в каталог» в профиле: фильтры и страница сохраняются (ТЗ 7)
$_SESSION['catalog_back'] = 'photographers.php' . (($_SERVER['QUERY_STRING'] ?? '') !== '' ? '?' . $_SERVER['QUERY_STRING'] : '');

$pages = max(1, (int) ceil($total / CARDS_PER_PAGE));
$page  = max(1, min($pages, (int) ($_GET['page'] ?? 1)));
$items = array_slice($found, ($page - 1) * CARDS_PER_PAGE, CARDS_PER_PAGE);

// Параметры для ссылок пагинации (условия отражаются в адресе, ТЗ 8.3)
$linkParams = [
    'city' => $f['city'] !== DEFAULT_CITY ? $f['city'] : null,
    'genre' => $f['genres'],
    'price_min' => $f['price_min'] > PRICE_MIN ? $f['price_min'] : null,
    'price_max' => $f['price_max'] < PRICE_MAX ? $f['price_max'] : null,
    'night' => $f['night'] ? 1 : null, 'video' => $f['video'] ? 1 : null, 'trip' => $f['trip'] ? 1 : null,
    'sort' => $f['sort'] !== 'new' ? $f['sort'] : null,
];

page_start([
    'title'       => 'Фотографы',
    'description' => 'Каталог фотографов Якутии: фильтры по городу, жанру и цене, портфолио и цены «от». Найдите свадебного, семейного или портретного фотографа.',
    'nav'         => 'catalog',
    'canonical'   => 'photographers.php', // страницы с фильтрами указывают canonical на каталог (ТЗ 16.1)
]);
require __DIR__ . '/partials/header.php';

$sorts = ['new' => 'Сначала новые', 'price_asc' => 'Цена по возрастанию', 'price_desc' => 'Цена по убыванию'];
?>

<section class="section section--page">
  <div class="container">
    <div class="page-head">
      <div>
        <h1>Фотографы</h1>
        <p class="muted" data-found>Найдено: <?= $total ?> <?= plural($total, 'фотограф', 'фотографа', 'фотографов') ?></p>
      </div>
      <form method="get" class="sort" id="sort-form">
        <?php foreach ($linkParams as $k => $v): if ($k === 'sort' || $v === null) continue; foreach ((array) $v as $vv): ?>
          <input type="hidden" name="<?= e($k) . (is_array($v) ? '[]' : '') ?>" value="<?= e((string) $vv) ?>">
        <?php endforeach; endforeach; ?>
        <label class="sr-only" for="sort">Сортировка</label>
        <span class="input input--select input--pill"><select id="sort" name="sort" onchange="this.form.submit()">
          <?php foreach ($sorts as $k => $label): ?><option value="<?= $k ?>"<?= $f['sort'] === $k ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select><?= icon('chevron', 'icon--sm') ?></span>
        <noscript><button class="btn btn--ghost btn--sm">Применить</button></noscript>
      </form>
    </div>

    <button type="button" class="btn btn--ghost filters-toggle" aria-expanded="false" aria-controls="filters">
      <?= icon('sliders') ?>Фильтры
    </button>

    <div class="catalog">
      <form class="filters card" id="filters" method="get" action="photographers.php" data-filters>
        <div class="filters__head">
          <h2>Фильтры</h2>
          <a class="text-link" href="photographers.php">Сбросить</a>
        </div>
        <?php if ($f['sort'] !== 'new'): ?><input type="hidden" name="sort" value="<?= e($f['sort']) ?>"><?php endif; ?>

        <div class="field">
          <label for="f-city">Город</label>
          <span class="input input--select"><select id="f-city" name="city">
            <?php foreach (cities() as $c): ?><option<?= $f['city'] === $c ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
          </select><?= icon('chevron', 'icon--sm') ?></span>
        </div>

        <fieldset class="field">
          <legend>Жанр</legend>
          <?php foreach (genres() as $slug => $label): ?>
            <label class="check"><input type="checkbox" name="genre[]" value="<?= e($slug) ?>"<?= in_array($slug, $f['genres'], true) ? ' checked' : '' ?>><span class="check__box"><?= icon('check') ?></span><span><?= e($label) ?></span></label>
          <?php endforeach; ?>
        </fieldset>

        <fieldset class="field">
          <legend>Цена за час</legend>
          <div class="range" data-range data-min="<?= PRICE_MIN ?>" data-max="<?= PRICE_MAX ?>">
            <div class="range__track"><div class="range__fill"></div></div>
            <input type="range" name="price_min" min="<?= PRICE_MIN ?>" max="<?= PRICE_MAX ?>" step="100" value="<?= $f['price_min'] ?>" aria-label="Цена от">
            <input type="range" name="price_max" min="<?= PRICE_MIN ?>" max="<?= PRICE_MAX ?>" step="100" value="<?= $f['price_max'] ?>" aria-label="Цена до">
          </div>
          <div class="range__values mono"><output data-range-min><?= e(money($f['price_min'])) ?></output><output data-range-max><?= e(money($f['price_max'])) ?></output></div>
          <?php if ($f['price_error']): ?><p class="field__error is-visible" role="alert">Нижняя цена не может быть больше верхней</p><?php endif; ?>
        </fieldset>

        <fieldset class="field">
          <legend>Дополнительно</legend>
          <label class="switch"><input type="checkbox" name="night" value="1"<?= $f['night'] ? ' checked' : '' ?>><span class="switch__track"></span><span>Ночная съёмка</span></label>
          <label class="switch"><input type="checkbox" name="video" value="1"<?= $f['video'] ? ' checked' : '' ?>><span class="switch__track"></span><span>Видеосъёмка</span></label>
          <label class="switch"><input type="checkbox" name="trip" value="1"<?= $f['trip'] ? ' checked' : '' ?>><span class="switch__track"></span><span>Выезд в район</span></label>
        </fieldset>

        <button type="submit" class="btn btn--primary btn--block" data-show-count>Показать <?= $total ?></button>
        <a class="btn btn--ghost btn--block" href="photographers.php">Сбросить всё</a>
      </form>

      <div class="catalog__results">
        <?php if ($total === 0): ?>
          <div class="empty card">
            <h2>Ничего не найдено</h2>
            <p class="muted">Попробуйте изменить условия поиска.</p>
            <a class="btn btn--primary" href="photographers.php">Сбросить всё</a>
          </div>
        <?php else: ?>
          <div class="grid grid--2">
            <?php foreach ($items as $p): ?><?= photographer_card($p) ?><?php endforeach; ?>
          </div>
          <?= pagination($page, $pages, 'photographers.php', $linkParams) ?>
        <?php endif; ?>
        <?= price_note() ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
