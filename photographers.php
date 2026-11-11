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
$activeFilters = count($f['genres']) + (int) $f['night'] + (int) $f['video'] + (int) $f['trip']
    + ($f['city'] !== DEFAULT_CITY ? 1 : 0) + (($f['price_min'] > PRICE_MIN || $f['price_max'] < PRICE_MAX) ? 1 : 0);
require __DIR__ . '/partials/header.php';

$sorts = ['new' => 'Сначала новые', 'price_asc' => 'Цена по возрастанию', 'price_desc' => 'Цена по убыванию'];
?>

<section class="ph">
  <div class="ph__row">
    <div>
      <h1>Фотографы</h1>
      <p class="cnt" data-found>Найдено: <?= $total ?> <?= plural($total, 'фотограф', 'фотографа', 'фотографов') ?> · <?= e($f['city'] ?: 'все города') ?></p>
    </div>
    <form method="get" class="sort" id="sort-form">
      <?php foreach ($linkParams as $k => $v): if ($k === 'sort' || $v === null) continue; foreach ((array) $v as $vv): ?>
        <input type="hidden" name="<?= e($k) . (is_array($v) ? '[]' : '') ?>" value="<?= e((string) $vv) ?>">
      <?php endforeach; endforeach; ?>
      <div class="field">
        <label for="sort"><small>Сортировка</small></label>
        <div class="v v--select"><select id="sort" name="sort" onchange="this.form.submit()">
          <?php foreach ($sorts as $k => $label): ?><option value="<?= $k ?>"<?= $f['sort'] === $k ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
        </select><?= icon('chevron') ?></div>
      </div>
      <noscript><button class="btn btn--sm">Применить</button></noscript>
    </form>
  </div>
</section>

<div class="cat">
  <button type="button" class="filters-toggle" aria-expanded="false" aria-controls="filters"><?= icon('sliders') ?>Фильтры<?= $activeFilters ? ' · ' . $activeFilters : '' ?></button>

  <form class="flt" id="filters" method="get" action="photographers.php" data-filters aria-label="Фильтры каталога">
    <div class="flt__head"><h2>Фильтры</h2><button type="button" class="md__close" data-close-filters aria-label="Закрыть фильтры"><?= icon('x') ?></button></div>

    <div class="field only-m">
      <label for="f-sort"><small>Сортировка</small></label>
      <div class="v v--select"><select id="f-sort" name="sort">
        <?php foreach ($sorts as $k => $label): ?><option value="<?= $k ?>"<?= $f['sort'] === $k ? ' selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
      </select><?= icon('chevron') ?></div>
    </div>

    <div>
      <h2><label for="f-city">Город</label></h2>
      <div class="v v--select"><select id="f-city" name="city">
        <?php foreach (cities() as $c): ?><option<?= $f['city'] === $c ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
      </select><?= icon('chevron') ?></div>
    </div>

    <fieldset class="field">
      <h2>Жанр</h2>
      <div class="gl">
        <?php foreach (genres() as $slug => $label): ?>
          <label class="chk"><input type="checkbox" name="genre[]" value="<?= e($slug) ?>"<?= in_array($slug, $f['genres'], true) ? ' checked' : '' ?>><s><?= icon('check') ?></s><span><?= e($label) ?></span></label>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <fieldset class="field">
      <h2>Цена за час</h2>
      <div class="range" data-range data-min="<?= PRICE_MIN ?>" data-max="<?= PRICE_MAX ?>">
        <div class="range__track"><div class="range__fill"></div></div>
        <input type="range" name="price_min" min="<?= PRICE_MIN ?>" max="<?= PRICE_MAX ?>" step="100" value="<?= $f['price_min'] ?>" aria-label="Цена от">
        <input type="range" name="price_max" min="<?= PRICE_MIN ?>" max="<?= PRICE_MAX ?>" step="100" value="<?= $f['price_max'] ?>" aria-label="Цена до">
      </div>
      <div class="range__values mono"><output data-range-min><?= e(money($f['price_min'])) ?></output><output data-range-max><?= e(money($f['price_max'])) ?></output></div>
      <?php if ($f['price_error']): ?><p class="err" role="alert">Нижняя цена не может быть больше верхней</p><?php endif; ?>
    </fieldset>

    <fieldset class="field">
      <h2>Дополнительно</h2>
      <div class="gl">
        <label class="tg"><input type="checkbox" name="night" value="1"<?= $f['night'] ? ' checked' : '' ?>><s></s><span>Ночная съёмка</span></label>
        <label class="tg"><input type="checkbox" name="video" value="1"<?= $f['video'] ? ' checked' : '' ?>><s></s><span>Видеосъёмка</span></label>
        <label class="tg"><input type="checkbox" name="trip" value="1"<?= $f['trip'] ? ' checked' : '' ?>><s></s><span>Выезд в район</span></label>
      </div>
    </fieldset>

    <div style="display:grid;gap:10px">
      <button type="submit" class="btn btn--primary btn--block" data-show-count>Показать <?= $total ?></button>
      <a class="btn btn--block" href="photographers.php">Сбросить всё</a>
    </div>
  </form>

  <div>
    <?php if ($total === 0): ?>
      <div class="empty">
        <h2>Ничего не найдено</h2>
        <p class="mt">Попробуйте изменить условия поиска.</p>
        <a class="btn btn--primary" href="photographers.php">Сбросить всё</a>
      </div>
    <?php else: ?>
      <div class="pcs">
        <?php foreach ($items as $p): ?><?= photographer_card($p) ?><?php endforeach; ?>
      </div>
      <?= pagination($page, $pages, 'photographers.php', $linkParams) ?>
    <?php endif; ?>
    <?= price_note() ?>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
