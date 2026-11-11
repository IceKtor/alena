<?php
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

page_start([
    'title'       => 'Фотографы Якутии',
    'description' => 'Портфолио фотографов Якутии: смотрите работы, сравнивайте цены и пишите фотографу напрямую. Фотографам — бесплатная страница с портфолио.',
    'nav'         => 'home',
    'body_class'  => 'home',
]);

$fresh   = array_slice(all_works(), 0, 12);
$people  = array_slice(photographers(), 0, 6);
$heroW   = (array_values(array_filter(all_works(), fn($w) => $w['file'] === 'road.jpg'))[0]) ?? all_works()[0]; // самое крупное фото
require __DIR__ . '/partials/header.php';
?>

<section class="hero">
  <div class="hero__text">
    <h1>Фотографы Якутии</h1>
    <p class="lead">Сначала смотрите снимки, потом пишите автору. Если снимаете сами, заведите бесплатную страницу с портфолио.</p>

    <!-- Быстрый поиск (ФТ-01): одна фраза, слова выбираются из списков → каталог с этими условиями -->
    <form class="ask" action="photographers.php" method="get">
      <p class="ask__line">Мне нужен фотограф
        <select name="city" aria-label="Город">
          <?php foreach (cities() as $c): ?><option value="<?= e($c) ?>"<?= $c === DEFAULT_CITY ? ' selected' : '' ?>><?= e(city_in($c)) ?></option><?php endforeach; ?>
        </select>
        <select name="genre[]" aria-label="Жанр">
          <option value="">для любой съёмки</option>
          <?php foreach (genres() as $slug => $label): ?><option value="<?= e($slug) ?>"><?= e(genre_for($slug)) ?></option><?php endforeach; ?>
        </select>
        <select name="price_max" aria-label="Бюджет за час">
          <option value="<?= PRICE_MAX ?>">с любым бюджетом</option>
          <?php foreach ([3000, 5000, 8000] as $pm): ?><option value="<?= $pm ?>">до <?= e(money($pm)) ?> в час</option><?php endforeach; ?>
        </select>
      </p>
      <button type="submit" class="btn btn--primary btn--lg">Показать фотографов</button>
    </form>
  </div>

  <figure class="hero__photo">
    <a href="<?= e(url('photographer.php', ['id' => $heroW['author_id']])) ?>">
      <?= img_tag($heroW['file'], $heroW['title'] . ' — ' . $heroW['author'], '', false) ?>
      <figcaption><?= e($heroW['title']) ?>, <?= e($heroW['author']) ?></figcaption>
    </a>
  </figure>
</section>

<section class="sec">
  <h2>Новые работы</h2>
  <p class="sub">Сверху самые свежие.</p>
  <div class="mas">
    <?php foreach ($fresh as $w): ?><?= work_figure($w) ?><?php endforeach; ?>
  </div>
  <div class="center"><a class="btn" href="works.php">Смотреть все работы</a></div>
</section>

<section class="sec" id="how">
  <h2>Фотографы</h2>
  <p class="sub"><?= count(photographers()) ?> фотографов в каталоге.</p>
  <div class="rows">
    <?php foreach ($people as $i => $p): ?><?= photographer_row($p, $i + 1) ?><?php endforeach; ?>
  </div>
  <div class="center"><a class="btn" href="photographers.php">Открыть каталог</a></div>
</section>

<?= cta_block() ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
