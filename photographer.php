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
    'body_class'  => $isOwn ? '' : 'has-stick',
]);
require __DIR__ . '/partials/header.php';
?>

<div class="cover">
  <?= img_tag('cover.jpg', 'Обложка профиля', '', false) ?>
  <a class="crumb-back" href="<?= e($back) ?>">Назад в каталог</a>
</div>

<section class="pf">
  <div class="pf__info">
    <img class="avatar avatar--xl" src="<?= e(photo_url($p['avatar'])) ?>" alt="<?= e($p['name']) ?>" width="150" height="150">
    <h1><?= e($p['name']) ?></h1>
    <p class="pf__meta">
      <span class="city"><?= icon('pin') ?><?= e($p['city']) ?>, опыт <?= e($expText) ?></span>
      <span class="genres"><?php foreach ($p['genres'] as $i => $g): ?><?= $i ? ', ' : '' ?><a href="<?= e(url('photographers.php', ['genre' => [$g]])) ?>"><?= e($i ? mb_strtolower(genre_name($g)) : genre_name($g)) ?></a><?php endforeach; ?></span>
    </p>
  </div>
  <div class="pf__cta">
    <?php if ($isOwn): ?>
      <a class="btn" href="cabinet.php?section=profile">Редактировать профиль</a>
    <?php else: ?>
      <div class="pf__buttons"><?= fav_button($p['id'], 'Добавить в избранное') ?><button type="button" class="btn btn--primary btn--lg" data-open-request data-id="<?= $p['id'] ?>" data-name="<?= e($p['name']) ?>" data-dative="<?= e(dative_first_name($p['name'])) ?>" data-avatar="<?= e(photo_url($p['avatar'])) ?>">Связаться</button></div>
    <?php endif; ?>
    <span class="pr">от <?= e(money($p['price'])) ?> в час</span>
  </div>
</section>

<div class="pf-tabs">
  <div class="tabs" role="tablist" aria-label="Разделы профиля">
    <button type="button" class="tab" role="tab" id="tab-portfolio" aria-controls="panel-portfolio" aria-selected="true">Портфолио</button>
    <button type="button" class="tab" role="tab" id="tab-about" aria-controls="panel-about" aria-selected="false" tabindex="-1">Об авторе</button>
    <button type="button" class="tab" role="tab" id="tab-services" aria-controls="panel-services" aria-selected="false" tabindex="-1">Услуги и цены</button>
    <button type="button" class="tab" role="tab" id="tab-reviews" aria-controls="panel-reviews" aria-selected="false" tabindex="-1">Отзывы</button>
  </div>
</div>

<div class="pbody">
  <div>
    <div role="tabpanel" id="panel-portfolio" aria-labelledby="tab-portfolio">
      <div class="mas" data-gallery data-step="<?= GALLERY_STEP ?>">
        <?php foreach ($works as $i => $w): ?>
          <figure<?= ratio_style($w['file']) ?><?= $i >= GALLERY_STEP ? ' hidden' : '' ?>>
            <button type="button" data-lightbox="<?= e(photo_url($w['file'])) ?>" data-caption="<?= e($w['title'] . ' — ' . genre_name($w['genre'])) ?>">
              <?= img_tag($w['file'], $w['title'] . ' — ' . $p['name']) ?>
            </button>
          </figure>
        <?php endforeach; ?>
      </div>
      <?php if (count($works) > GALLERY_STEP): ?>
        <div class="center"><button type="button" class="btn" data-gallery-more>Показать ещё</button></div>
      <?php endif; ?>
    </div>

    <div class="panel" role="tabpanel" id="panel-about" aria-labelledby="tab-about" hidden>
      <h2>Об авторе</h2>
      <p><?= e($p['about']) ?></p>
      <p>Стаж: <?= e($expText) ?>. Город: <?= e($p['city']) ?>. Жанры: <?= e(mb_strtolower(genre_names($p['genres']))) ?>.</p>
    </div>

    <div class="panel" role="tabpanel" id="panel-services" aria-labelledby="tab-services" hidden>
      <h2>Услуги и цены</h2>
      <ul class="pl">
        <?php foreach ($services as $s): ?><li><span><?= e($s['title']) ?></span><span class="pr">от <?= e(money($s['price'])) ?></span></li><?php endforeach; ?>
      </ul>
      <?= price_note() ?>
    </div>

    <div class="panel" role="tabpanel" id="panel-reviews" aria-labelledby="tab-reviews" hidden>
      <!-- ЗАГЛУШКА (ФТ-16, желательное): отзывы. TODO(DB): таблица reviews; без вымышленных отзывов, микроразметку рейтингов не применять (ТЗ 16.1). -->
      <h2>Отзывов пока нет</h2>
      <p>Раздел появится вместе с базой данных.</p>
    </div>
  </div>

  <aside class="side">
    <section aria-labelledby="side-dates">
      <h2 id="side-dates">Свободные даты</h2>
      <ul class="dates">
        <?php foreach ($p['dates'] as $i => $d): ?><li class="dt<?= $i === 1 ? ' on' : '' ?>"><?= $d ?></li><?php endforeach; ?>
      </ul>
      <p class="small"><?= e($p['dates_note']) ?></p>
    </section>
  </aside>
</div>

<?php if (!$isOwn): ?>
  <div class="stick">
    <div><strong>от <?= e(money($p['price'])) ?> в час</strong><small>Ответит в течение дня</small></div>
    <button type="button" class="btn btn--primary" data-open-request data-id="<?= $p['id'] ?>" data-name="<?= e($p['name']) ?>" data-dative="<?= e(dative_first_name($p['name'])) ?>" data-avatar="<?= e(photo_url($p['avatar'])) ?>">Связаться</button>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
