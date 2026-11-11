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
      <h1>Найдите фотографа <em>по его кадрам</em></h1>
      <p class="lead">Смотрите работы, сравнивайте цены и пишите фотографу напрямую. Если вы сами снимаете — заведите страницу с портфолио, это бесплатно.</p>

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
    <div class="mosaic">
      <?php foreach ($fresh as $w): ?><?= work_card($w) ?><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="how">
  <div class="container">
    <h2>Как это устроено</h2>
    <ol class="steps">
      <li class="step"><span class="step__num" aria-hidden="true">1</span><h3>Заведите профиль</h3><p class="muted">Имя, город, жанры и сколько стоит час съёмки. Больше ничего не нужно.</p></li>
      <li class="step"><span class="step__num" aria-hidden="true">2</span><h3>Выложите кадры</h3><p class="muted">Те, по которым вас должны узнавать. Остальное заказчик спросит сам.</p></li>
      <li class="step"><span class="step__num" aria-hidden="true">3</span><h3>Отвечайте на заявки</h3><p class="muted">Заказчик находит вас в каталоге и пишет напрямую — заявки собираются в кабинете.</p></li>
    </ol>
  </div>
</section>

<section class="cta">
  <div class="container cta__inner">
    <div>
      <h2>Снимаете? Покажите, что умеете</h2>
      <p>Профиль бесплатный, заводится за пару минут.</p>
    </div>
    <button type="button" class="btn btn--dark btn--lg" data-open-modal="register">Создать профиль</button>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
