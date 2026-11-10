<?php
/** Профиль фотографа (ФТ-05, ФТ-16, ФТ-19). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

$p = photographer_by_id((int) ($_GET['id'] ?? 0));
if (!$p) {
    not_found();
}
$works    = photographer_works($p);
$services = photographer_services($p);
$back     = $_SESSION['catalog_back'] ?? 'photographers.php';
$isOwn    = is_role(ROLE_PHOTOGRAPHER) && (int) current_user()['id'] === $p['id'];
$expText  = $p['exp'] . ' ' . plural($p['exp'], 'год', 'года', 'лет');

// TODO(DB): записывать факт просмотра профиля для статистики (ФТ-09): INSERT INTO profile_views.

page_start([
    'title'       => $p['name'] . ' — фотограф, ' . $p['city'],
    'description' => $p['name'] . ': ' . mb_strtolower(genre_names($p['genres'])) . ' съёмка, ' . $p['city'] . '. Портфолио, услуги и цены от ' . $p['price'] . ' ₽ за час.',
    'nav'         => 'catalog',
]);
require __DIR__ . '/partials/header.php';
?>

<div class="profile-cover">
  <?= img_tag('cover.jpg', 'Обложка профиля', '', false) ?>
</div>
<?= stripe() ?>

<section class="profile">
  <div class="container">
    <p class="crumbs"><a class="text-link" href="<?= e($back) ?>">← Назад в каталог</a></p>
    <div class="profile__head">
      <img class="avatar avatar--xl" src="<?= e(photo_url($p['avatar'])) ?>" alt="<?= e($p['name']) ?>" width="180" height="180">
      <div class="profile__info">
        <h1><?= e($p['name']) ?></h1>
        <p class="muted profile__city"><?= icon('pin') ?><?= e($p['city']) ?>, опыт <?= e($expText) ?></p>
        <ul class="tags">
          <?php foreach ($p['genres'] as $i => $g): ?><li><a class="chip<?= $i === 0 ? ' chip--on' : '' ?>" href="<?= e(url('photographers.php', ['genre' => [$g]])) ?>"><?= e(genre_name($g)) ?></a></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="profile__cta">
        <?php if ($isOwn): ?>
          <a class="btn btn--dark" href="cabinet.php?section=profile">Редактировать профиль</a>
        <?php else: ?>
          <button type="button" class="btn btn--primary btn--lg" data-open-request data-id="<?= $p['id'] ?>" data-name="<?= e($p['name']) ?>" data-avatar="<?= e(photo_url($p['avatar'])) ?>">Связаться</button>
        <?php endif; ?>
        <p class="mono profile__price">от <?= e(money($p['price'])) ?> за час</p>
        <?php if (!$isOwn): ?><?= fav_button($p['id'], 'Добавить в избранное') ?><?php endif; ?>
      </div>
    </div>

    <div class="tabs" role="tablist" aria-label="Разделы профиля">
      <button type="button" role="tab" id="tab-portfolio" aria-controls="panel-portfolio" aria-selected="true">Портфолио</button>
      <button type="button" role="tab" id="tab-about" aria-controls="panel-about" aria-selected="false" tabindex="-1">Об авторе</button>
      <button type="button" role="tab" id="tab-services" aria-controls="panel-services" aria-selected="false" tabindex="-1">Услуги и цены</button>
      <button type="button" role="tab" id="tab-reviews" aria-controls="panel-reviews" aria-selected="false" tabindex="-1">Отзывы</button>
    </div>

    <div class="profile__grid">
      <div class="profile__main">
        <div role="tabpanel" id="panel-portfolio" aria-labelledby="tab-portfolio">
          <div class="masonry" data-gallery data-step="<?= GALLERY_STEP ?>">
            <?php foreach ($works as $i => $w): ?>
              <button type="button" class="masonry__item frame" data-lightbox="<?= e(photo_url($w['file'])) ?>" data-caption="<?= e($w['title'] . ' — ' . genre_name($w['genre'])) ?>"<?= $i >= GALLERY_STEP ? ' hidden' : '' ?>>
                <?= img_tag($w['file'], $w['title'] . ' — ' . $p['name']) ?>
              </button>
            <?php endforeach; ?>
          </div>
          <?php if (count($works) > GALLERY_STEP): ?>
            <div class="center"><button type="button" class="btn btn--ghost" data-gallery-more>Показать ещё</button></div>
          <?php endif; ?>
        </div>

        <div role="tabpanel" id="panel-about" aria-labelledby="tab-about" hidden>
          <div class="card card--pad"><h2 class="h3">Об авторе</h2><p><?= e($p['about']) ?></p>
            <p class="muted">Стаж: <?= e($expText) ?>. Город: <?= e($p['city']) ?>. Жанры: <?= e(mb_strtolower(genre_names($p['genres']))) ?>.</p></div>
        </div>

        <div role="tabpanel" id="panel-services" aria-labelledby="tab-services" hidden>
          <div class="card card--pad"><h2 class="h3">Услуги и цены</h2>
            <ul class="price-list">
              <?php foreach ($services as $s): ?><li><span><?= e($s['title']) ?></span><span class="mono">от <?= e(money($s['price'])) ?></span></li><?php endforeach; ?>
            </ul>
            <?= price_note() ?>
          </div>
        </div>

        <div role="tabpanel" id="panel-reviews" aria-labelledby="tab-reviews" hidden>
          <!-- ЗАГЛУШКА (ФТ-16, желательное): отзывы. TODO(DB): таблица reviews; без вымышленных отзывов, микроразметку рейтингов не применять (ТЗ 16.1). -->
          <div class="card card--pad empty"><h2 class="h3">Отзывов пока нет</h2><p class="muted">Раздел появится вместе с базой данных.</p></div>
        </div>
      </div>

      <aside class="profile__side">
        <section class="card card--pad" aria-labelledby="side-services">
          <h2 class="h3" id="side-services">Услуги и цены</h2>
          <ul class="price-list">
            <?php foreach ($services as $s): ?><li><span><?= e($s['title']) ?></span><span class="mono">от <?= e(money($s['price'])) ?></span></li><?php endforeach; ?>
          </ul>
          <?php if (!$isOwn): ?>
            <button type="button" class="btn btn--primary btn--block" data-open-request data-id="<?= $p['id'] ?>" data-name="<?= e($p['name']) ?>" data-avatar="<?= e(photo_url($p['avatar'])) ?>">Связаться</button>
          <?php endif; ?>
          <?= price_note() ?>
        </section>
        <section class="card card--pad" aria-labelledby="side-dates">
          <h2 class="h3" id="side-dates">Свободные даты</h2>
          <ul class="dates">
            <?php foreach ($p['dates'] as $i => $d): ?><li><span class="date-dot<?= $i === 1 ? ' date-dot--on' : '' ?>"><?= $d ?></span></li><?php endforeach; ?>
          </ul>
          <p class="muted small"><?= e($p['dates_note']) ?></p>
        </section>
        <section class="card card--pad" aria-labelledby="side-about">
          <h2 class="h3" id="side-about">Об авторе</h2>
          <p class="muted"><?= e($p['about']) ?></p>
        </section>
      </aside>
    </div>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
