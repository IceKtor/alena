<?php
/** Страница «Работы»: общая лента свежих работ (ФТ-06). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

$genreMap = [ // метки жанров ленты (ТЗ 10.5); «Свадьба» = «Свадебная»
    'portrait' => 'Портрет', 'landscape' => 'Пейзаж', 'reportage' => 'Репортаж',
    'still' => 'Предметная', 'wedding' => 'Свадьба',
];
$genre = (string) ($_GET['genre'] ?? '');
$genre = isset($genreMap[$genre]) ? $genre : '';
$shown = max(WORKS_PER_STEP, min(500, (int) ($_GET['shown'] ?? WORKS_PER_STEP)));
$shown = (int) (ceil($shown / WORKS_PER_STEP) * WORKS_PER_STEP);

$all   = all_works($genre ?: null);
$items = array_slice($all, 0, $shown);
$more  = count($all) > $shown;

page_start([
    'title'       => 'Работы',
    'description' => 'Лента свежих работ фотографов Якутии: портреты, пейзажи, репортажи. Выбирайте исполнителя по стилю.',
    'nav'         => 'works',
]);
require __DIR__ . '/partials/header.php';
?>

<section class="ph">
  <h1>Работы</h1>
  <p class="cnt">Лента обновляется ежедневно — новые кадры сверху.</p>
</section>

<nav class="filters-row" aria-label="Жанры">
  <a class="tab<?= $genre === '' ? ' on' : '' ?>" href="works.php"<?= $genre === '' ? ' aria-current="true"' : '' ?>>Все</a>
  <?php foreach ($genreMap as $slug => $label): ?>
    <a class="tab<?= $genre === $slug ? ' on' : '' ?>" href="<?= e(url('works.php', ['genre' => $slug])) ?>"<?= $genre === $slug ? ' aria-current="true"' : '' ?>><?= e($label) ?></a>
  <?php endforeach; ?>
</nav>

<div class="wrap">
  <?php if (!$items): ?>
    <div class="empty"><h2>Работ пока нет</h2><p class="mt">В этом жанре пока ничего не опубликовано.</p><a class="btn btn--primary" href="works.php">Показать все</a></div>
  <?php else: ?>
    <div class="mas">
      <?php foreach ($items as $w): ?><?= work_figure($w, true, $w['hours_ago'] < 24) ?><?php endforeach; ?>
    </div>
    <?php if ($more): ?>
      <div class="center"><a class="btn" href="<?= e(url('works.php', ['genre' => $genre, 'shown' => $shown + WORKS_PER_STEP])) ?>">Показать ещё</a></div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?= cta_block('Хотите, чтобы вас <em>увидели?</em>', 'Профиль бесплатный, заводится за пару минут.') ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
