<?php
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

page_start([
    'title'       => 'Найдите фотографа по его кадрам',
    'description' => 'Портфолио фотографов Якутии: сравнивайте стили и цены, выбирайте по жанру и пишите напрямую. Фотографам — страница с работами за минуту.',
    'nav'         => 'home',
]);

$fresh  = array_slice(all_works(), 0, 6);
$genres = genres();
require __DIR__ . '/partials/header.php';
?>

<section class="hero">
  <div class="container hero__grid">
    <div class="hero__text">
      <h1>Найдите фотографа по его кадрам</h1>
      <p class="lead">Смотрите портфолио, сравнивайте стили и цены, пишите напрямую. Фотографам — страница с работами за одну минуту.</p>

      <!-- Быстрый поиск (ФТ-01) → каталог с выбранными условиями -->
      <form class="quick-search" action="photographers.php" method="get">
        <div class="quick-search__field">
          <label for="qs-city">Город</label>
          <select id="qs-city" name="city">
            <?php foreach (cities() as $c): ?><option<?= $c === DEFAULT_CITY ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="quick-search__field">
          <label for="qs-genre">Жанр</label>
          <select id="qs-genre" name="genre[]">
            <option value="">Любой жанр</option>
            <?php foreach ($genres as $slug => $label): ?><option value="<?= e($slug) ?>"><?= e($label) ?></option><?php endforeach; ?>
          </select>
        </div>
        <button type="submit" class="btn btn--primary">Найти</button>
      </form>

      <div class="quick-tags">
        <span class="muted">Частые запросы</span>
        <?php foreach (['portrait', 'landscape', 'reportage', 'still'] as $g): ?>
          <a class="chip" href="<?= e(url('photographers.php', ['genre' => [$g]])) ?>"><?= e($genres[$g]) ?></a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="hero__art" aria-hidden="true">
      <span class="badge badge--amber hero__badge">Новое</span>
      <figure class="polaroid polaroid--a"><?= img_tag('lighthouse.jpg', '', '', false) ?></figure>
      <figure class="polaroid polaroid--b"><?= img_tag('hat.jpg', '', '', false) ?><figcaption>Лето</figcaption></figure>
      <figure class="polaroid polaroid--c"><?= img_tag('pier.jpg', '', '', false) ?><figcaption>Закат</figcaption></figure>
    </div>
  </div>
</section>
<?= stripe() ?>

<section class="section">
  <div class="container">
    <div class="section__head">
      <h2>Свежие работы</h2>
      <a class="more-link" href="works.php">Смотреть все</a>
    </div>
    <div class="grid grid--3">
      <?php foreach ($fresh as $w): ?><?= work_card($w) ?><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="how">
  <div class="container">
    <h2>Как это работает</h2>
    <ol class="steps">
      <li class="card step"><span class="step__num step__num--orange">1</span><h3>Создайте профиль</h3><p class="muted">Расскажите о себе, укажите жанры, город и стоимость часа.</p></li>
      <li class="card step"><span class="step__num step__num--amber">2</span><h3>Загрузите работы</h3><p class="muted">Добавьте лучшие кадры: они станут витриной для заказчиков.</p></li>
      <li class="card step"><span class="step__num step__num--teal">3</span><h3>Получайте заявки</h3><p class="muted">Заказчики находят вас через поиск и фильтры и пишут напрямую.</p></li>
    </ol>
  </div>
</section>

<section class="cta">
  <div class="container cta__inner">
    <div>
      <h2>Разместите портфолио за минуту</h2>
      <p>Профиль бесплатный: загрузите работы и ждите заявки.</p>
    </div>
    <button type="button" class="btn btn--dark btn--lg" data-open-modal="register">Создать профиль</button>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
