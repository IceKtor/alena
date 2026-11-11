<?php
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

page_start([
    'title'       => 'Фотографы Якутии — по их кадрам',
    'description' => 'Портфолио фотографов Якутии: смотрите работы, сравнивайте цены и пишите фотографу напрямую. Фотографам — бесплатная страница с портфолио.',
    'nav'         => 'home',
    'over'        => true,
    'body_class'  => 'home',
]);

$fresh  = array_slice(all_works(), 0, 12);
$genres = genres();
$people = array_slice(photographers(), 0, 6);
require __DIR__ . '/partials/header.php';
?>

<section class="hero">
  <img src="<?= e(photo_url('road.jpg')) ?>" alt="" width="1378" height="832">
  <div class="dim"></div>
  <div class="hc">
    <h1>Фотографы Якутии — <em>по их кадрам</em></h1>
    <p class="lead">Смотрите работы, сравнивайте цены и пишите фотографу напрямую. Если снимаете сами — заведите страницу, это бесплатно.</p>

    <!-- Быстрый поиск (ФТ-01) → каталог с выбранными условиями -->
    <form class="sline" action="photographers.php" method="get">
      <div class="field">
        <label for="qs-city"><small>Город</small></label>
        <div class="v v--select"><select id="qs-city" name="city">
          <?php foreach (cities() as $c): ?><option<?= $c === DEFAULT_CITY ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
        </select><?= icon('chevron') ?></div>
      </div>
      <div class="field">
        <label for="qs-genre"><small>Жанр</small></label>
        <div class="v v--select"><select id="qs-genre" name="genre[]">
          <option value="">Любой</option>
          <?php foreach ($genres as $slug => $label): ?><option value="<?= e($slug) ?>"><?= e($label) ?></option><?php endforeach; ?>
        </select><?= icon('chevron') ?></div>
      </div>
      <div class="field">
        <label for="qs-price"><small>Бюджет за час</small></label>
        <div class="v v--select"><select id="qs-price" name="price_max">
          <option value="<?= PRICE_MAX ?>">до <?= e(money(PRICE_MAX)) ?></option>
          <?php foreach ([3000, 5000, 8000] as $pm): ?><option value="<?= $pm ?>">до <?= e(money($pm)) ?></option><?php endforeach; ?>
        </select><?= icon('chevron') ?></div>
      </div>
      <button type="submit" class="btn btn--primary">Найти фотографа</button>
    </form>
  </div>
</section>

<section class="sec">
  <h2>Свежие кадры</h2>
  <p class="sub">Последние публикации, новые сверху</p>
  <div class="mas">
    <?php foreach ($fresh as $w): ?><?= work_figure($w) ?><?php endforeach; ?>
  </div>
  <div class="center"><a class="btn" href="works.php">Все работы</a></div>
</section>

<section class="sec" id="how">
  <h2>Фотографы</h2>
  <p class="sub"><?= count(photographers()) ?> в каталоге · <?= e(DEFAULT_CITY) ?></p>
  <div class="rows">
    <?php foreach ($people as $i => $p): ?><?= photographer_row($p, $i + 1) ?><?php endforeach; ?>
  </div>
  <div class="center"><a class="btn" href="photographers.php">Весь каталог</a></div>
</section>

<?= cta_block() ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
